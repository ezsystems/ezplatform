#!/bin/bash
# Script to generate virtual host config based on template containing variables among the once define below.
# For help text, execute: ./bin/vhost.sh -h

# Available option variables, configurable by user
declare -a option_vars=(
    %BASEDIR%
    %CLASSLOADER_FILE%
    %DEBUG%
    %ENV%
    %HOST_ALIAS%
    %HTTP_CACHE%
    %HTTP_CACHE_CLASS%
    %HOST_NAME%
    %IP_ADDRESS%
    %PORT%
    %REVERSE_PROXIES%
    %BODY_SIZE_LIMIT%
    %TIMEOUT%
    %FASTCGI_PASS%
)

# Available template variables
declare -a template_vars
# copy option_vars
template_vars=(${option_vars[*]})
# The additinal vars are calculated by script
template_vars+=("%BODY_SIZE_LIMIT_M%")
template_vars+=("%TIMEOUT_S%")
template_vars+=("%HOST_LIST%")

# Default options
declare -a template_values=(
    ""
    ""
    ""
    "prod"
    "*.localhost"
    ""
    ""
    "localhost"
    '*'
    "80"
    ""
    "49152"
    "60"
    "unix:/var/run/php5-fpm.sock"
    "48m"
    "60s"
    "localhost *.localhost"
)

function show_help
{
    local env_list
    # Errors
    if [[ "$1" != "" ]] ;
    then
        if [[ "$2" != "" ]] ; then
            echo "ERROR: Argument '${1}' is required"
        else
            echo "ERROR: Argument '${1}' is invalid"
        fi
        echo ""
    fi

    env_list="${option_vars[@]//\%/}"
    env_list="${env_list// /, }"

    # General help text
    cat << EOF
Script for generating httpd config based on simplified templates

Help (this text):
./bin/vhost.sh [-h|--help]

Usage:
./bin/vhost.sh --basedir=/var/www/ezplatform \\
  --template-file=doc/apache2/vhost.template \\
  > /etc/apache/site-enabled/my-site

Defaults values will be fetched from the environment variables $env_list, but might be overriden using the arguments listed below.

Arguments:
  --basedir=<path>                      : Root path to where the eZ installation is placed, used for <path>/web
  --template-file=<file.template>       : The file to use as template for the generated ouput file
  [--env=prod|dev|..]                   : Symfony environment used for the virtual host, default is "prod"
  [--host-name=localhost]               : Primary host name, default "localhost"
  [--host-alias=*.localhost]            : Space separated list of host aliases, default "*.localhost"
  [--ip=*|127.0.0.1]                    : IP address web server should accept traffic on.
  [--port=80]                           : Port number web server should listen to.
  [--debug=0|1]                         : Set if Symfony debug should be on, by default on if env is "dev"
  [--reverse-proxies=127.0.0.1,....]    : Comma separated proxies (e.g. Varnish), will disable symfony proxy if set
  [--sf-proxy=0|1]                      : To disable Symfony HTTP cache Proxy for using a different reverse proxy
                                          By default disabled when evn is "dev", enabled otherwise.
  [--sf-proxy-class=<class-file.php>]   : To specify a different class then default to use as the Symfony proxy
  [--classloader-file=<class-file.php>] : To specify a different class then default to use for php auto loading
  [--body-size-limit=<int>]             : Limit in megabytes for max size of request body, 0 value disables limit.
  [--request-timeout=<int>]             : Limit in seconds before timeout of request, 0 value disables timeout limit.
  [-h|--help]                           : Help text, this one more or less

EOF
}

# This function checks if there variables like BASEDIR, CLASSLOADER_FILE etc exists ( checks all variables defined in option_vars )
# If environment variable exists, it's value is used as default when parsing template
function inject_environment_variables
{
    local current_env_variable
    local option_value
    local template_var
    local i

    i=0;
    for template_var in "${option_vars[@]}"; do
        # Remove "%" from from template_vars....
        current_env_variable=${template_var//%/}
        # Get value of variable referenced to by $current_env_variable. If env variable do not exists, value is set to ""
        option_value=${!current_env_variable:-SomeDefault}
        if [ "$option_value" != "SomeDefault" ]; then
            template_values[$i]="$option_value";
            if [ "$current_env_variable" == "BODY_SIZE_LIMIT" ]; then
                template_values[11]="$option_value"*1024
                template_values[14]="${option_value}m"
            fi
            if [ "$current_env_variable" == "TIMEOUT" ]; then
                template_values[12]="$option_value"
                template_values[15]="${option_value}s"
            fi
        fi
        let i=$i+1;
    done
}

inject_environment_variables

## Parse arguments
for i in "$@"
do
case $i in
    -b=*|--basedir=*)
        template_values[0]="${i#*=}"
        ;;
    --classloader-file=*)
        template_values[1]="${i#*=}"
        ;;
    -d=*|--debug=*)
        template_values[2]="${i#*=}"
        ;;
    -e=*|--env=*)
        template_values[3]="${i#*=}"
        ;;
    --host-alias=*)
        template_values[4]="${i#*=}"
        ;;
    --sf-proxy=*)
        template_values[5]="${i#*=}"
        ;;
    --sf-proxy-class=*)
        template_values[6]="${i#*=}"
        ;;
    --host-name=*)
        template_values[7]="${i#*=}"
        ;;
    --ip=*)
        template_values[8]="${i#*=}"
        ;;
    -p=*|--port=*)
        template_values[9]="${i#*=}"
        ;;
    --reverse-proxies=*)
        template_values[10]="${i#*=}"
        ;;
    --body-size-limit=*)
        template_values[11]="${i#*=}"*1024
        template_values[14]="${i#*=}m"
        ;;
    --request-timeout=*)
        template_values[12]="${i#*=}"
        template_values[15]="${i#*=}s"
        ;;
    -t=*|--template-file=*)
        template_file="${i#*=}"
        ;;
    -h|--help)
        show_help
        exit 0
        ;;
    *)
        show_help "${i}"
        exit 1
        ;;
esac
done


## Validation
if [ "$template_file}" == "" ] ; then
    show_help "--template-file=$template_file"
    exit 1
fi


if [ ! -f "$template_file" ] ; then
    show_help "--template-file=$template_file"
    exit 1
fi

if [[ "${template_values[0]}" == "" ]] ; then
    show_help "--basedir=<path>" true
    exit 1
fi

## Option specific logic

# For httpd server having just one host config we provide HOST_LIST
template_values[16]="${template_values[7]}"
if [[ "${template_values[4]}" != "" ]] ; then
     tmp="${template_values[16]} ${template_values[4]}"
     template_values[16]=$tmp
fi


## Generate template result and output

template=$(<$template_file)
COUNTER=0
while [  "${template_vars[$COUNTER]}" != "" ]; do
    tmp=${template//${template_vars[$COUNTER]}/${template_values[$COUNTER]}}
    template=$tmp
    let COUNTER=COUNTER+1
done

echo "$template"
