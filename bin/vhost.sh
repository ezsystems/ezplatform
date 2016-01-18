#!/bin/bash
# Script to generate virtual host config based on template containing variables among the once define below.
# For help text, execute: ./bin/vhost.sh -h

# Available template variables
declare -a template_vars=(
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
    %BODY_SIZE_LIMIT_M%
    %TIMEOUT_S%
    %HOST_LIST%
)

# Default options
declare -a option_values=(
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

    env_list="${template_vars[@]//\%/}"
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

# This function checks if there variables like BASEDIR, CLASSLOADER_FILE etc exists ( checks all variables defined in template_vars )
# If environment variable exists, it's value is used as default when parsing template
function inject_environment_variables
{
    local current_env_variable
    local option_value
    local template_var
    local i

    i=0;
    for template_var in "${template_vars[@]}"; do
        # Remove "%" from from template_vars....
        current_env_variable=${template_var//%/}
        # Get value of variable referenced to by $current_env_variable. If env variable do not exists, value is set to "SomeDefault"
        option_value=${!current_env_variable:-SomeDefault}
        if [ "$option_value" != "SomeDefault" ]; then
            option_values[$i]="$option_value";
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
        option_values[0]="${i#*=}"
        ;;
    --classloader-file=*)
        option_values[1]="${i#*=}"
        ;;
    -d=*|--debug=*)
        option_values[2]="${i#*=}"
        ;;
    -e=*|--env=*)
        option_values[3]="${i#*=}"
        ;;
    --host-alias=*)
        option_values[4]="${i#*=}"
        ;;
    --sf-proxy=*)
        option_values[5]="${i#*=}"
        ;;
    --sf-proxy-class=*)
        option_values[6]="${i#*=}"
        ;;
    --host-name=*)
        option_values[7]="${i#*=}"
        ;;
    --ip=*)
        option_values[8]="${i#*=}"
        ;;
    -p=*|--port=*)
        option_values[9]="${i#*=}"
        ;;
    --reverse-proxies=*)
        option_values[10]="${i#*=}"
        ;;
    --body-size-limit=*)
        option_values[11]="${i#*=}"*1024
        option_values[13]="${i#*=}m"
        ;;
    --request-timeout=*)
        option_values[12]="${i#*=}"
        option_values[14]="${i#*=}s"
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

if [[ "${option_values[0]}" == "" ]] ; then
    show_help "--basedir=<path>" true
    exit 1
fi

## Option specific logic

# For httpd server having just one host config we provide HOST_LIST
option_values[15]="${option_values[7]}"
if [[ "${option_values[4]}" != "" ]] ; then
     tmp="${option_values[15]} ${option_values[4]}"
     option_values[15]=$tmp
fi


## Generate template result and output

template=$(<$template_file)
COUNTER=0
while [  "${template_vars[$COUNTER]}" != "" ]; do
    tmp=${template//${template_vars[$COUNTER]}/${option_values[$COUNTER]}}
    template=$tmp
    let COUNTER=COUNTER+1
done

echo "$template"
