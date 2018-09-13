#!/bin/bash
# Script to generate virtual host config based on template containing variables among the ones defined below.
# For help text, execute: ./bin/vhost.sh -h

# Available option variables, configurable by user
declare -a option_vars=(
    %BASEDIR%
    %IP_ADDRESS%
    %PORT%
    %HOST_NAME%
    %HOST_ALIAS%
    %SYMFONY_ENV%
    %SYMFONY_DEBUG%
    %SYMFONY_HTTP_CACHE%
    %SYMFONY_TRUSTED_PROXIES%
    %BODY_SIZE_LIMIT%
    %TIMEOUT%
    %FASTCGI_PASS%
    %BINARY_DATA_HANDLER%
)

# Available template variables
declare -a template_vars
# copy option_vars
template_vars=(${option_vars[*]})
# The additional vars are calculated by script
template_vars+=("%BODY_SIZE_LIMIT_M%")
template_vars+=("%TIMEOUT_S%")
template_vars+=("%HOST_LIST%")

# Default options
declare -a template_values=(
    ""
    '*'
    "80"
    "localhost"
    "*.localhost"
    "prod"
    ""
    ""
    ""
    "50331648"
    "90"
    "unix:/var/run/php5-fpm.sock"
    ""
    "48m"
    "90s"
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
  | sudo tee /etc/apache2/sites-enabled/my-site > /dev/null

Default values will be fetched from the environment variables $env_list, but may be overridden using the arguments listed below.

Arguments:
  --template-file=<file.template>          : The file to use as a template for the generated output file
  [--basedir=<path>]                       : Root path to eZ installation, auto detected if command is run from root
  [--host-name=localhost]                  : Primary host name, default "localhost"
  [--host-alias=*.localhost]               : Space separated list of host aliases, default "*.localhost"
  [--ip=*|127.0.0.1]                       : IP address web server should accept traffic on.
  [--port=80]                              : Port number web server should listen to.
  [--sf-env=prod|dev|..]                   : Symfony environment used for the virtual host, default is "prod"
  [--sf-debug=0|1]                         : Set if Symfony debug should be on, by default on if env is "dev"
  [--sf-trusted-proxies=127.0.0.1,....]    : Comma separated trusted proxies (e.g. Varnish), that we can get client ip from
  [--sf-http-cache=0|1]                    : To disable Symfony HTTP cache Proxy for using a different reverse proxy
                                             By default disabled when evn is "dev", enabled otherwise.
  [--body-size-limit=<int>]                : Limit in megabytes for max size of request body, 0 value disables limit.
  [--request-timeout=<int>]                : Limit in seconds before timeout of request, 0 value disables timeout limit.
  [--binary-data-handler=dfs|]             : Name of handler in user. Specify "dfs" if you are using the dfs io handler.
  [-h|--help]                              : Help text, this one more or less

EOF
}

# This function checks if variables like BASEDIR, CLASSLOADER_FILE, etc. exist ( checks all variables defined in option_vars )
# If environment variable exists, its value is used as default when parsing template
function inject_environment_variables
{
    local current_env_variable
    local option_value
    local env_var
    local i

    i=0;
    for env_var in "${option_vars[@]}"; do
        # Remove "%" from from env_var....
        current_env_variable=${env_var//%/}
        # Get value of variable referenced to by $current_env_variable. If env variable does not exist, value is set to ""
        option_value=${!current_env_variable:-SomeDefault}
        if [ "$option_value" != "SomeDefault" ]; then
            template_values[$i]="$option_value";
            if [ "$current_env_variable" == "BODY_SIZE_LIMIT" ]; then
                let template_values[9]="$option_value"*1024
                template_values[13]="${option_value}m"
            fi
            if [ "$current_env_variable" == "TIMEOUT" ]; then
                template_values[10]="$option_value"
                template_values[14]="${option_value}s"
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
    --ip=*)
        template_values[1]="${i#*=}"
        ;;
    -p=*|--port=*)
        template_values[2]="${i#*=}"
        ;;
    --host-name=*)
        template_values[3]="${i#*=}"
        ;;
    --host-alias=*)
        template_values[4]="${i#*=}"
        ;;
    -e=*|--sf-env=*)
        template_values[5]="${i#*=}"
        ;;
    -d=*|--sf-debug=*)
        template_values[6]="${i#*=}"
        ;;
    --sf-http-cache=*)
        template_values[7]="${i#*=}"
        ;;
    --sf-trusted-proxies=*)
        template_values[8]="${i#*=}"
        ;;
    --binary-data-handler=*)
        template_values[12]="${i#*=}"
        ;;
    --body-size-limit=*)
        let template_values[9]="${i#*=}"*1024*1024
        template_values[13]="${i#*=}m"
        ;;
    --request-timeout=*)
        template_values[10]="${i#*=}"
        template_values[14]="${i#*=}s"
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
    if [ -d web/ ] ; then
        template_values[0]=`pwd`
    else
        show_help "--basedir=<path>" true
        exit 1
    fi
fi

## Option specific logic

# For httpd servers having just one host config we provide HOST_LIST
template_values[15]="${template_values[3]}"
if [[ "${template_values[4]}" != "" ]] ; then
     tmp="${template_values[15]} ${template_values[4]}"
     template_values[15]=$tmp
fi


## Generate template result and output

template=$(<$template_file)
COUNTER=0
while [  "${template_vars[$COUNTER]}" != "" ]; do
    current_var=${template_vars[$COUNTER]}
    current_value=${template_values[$COUNTER]}

    # Replace %VAR% with the actual value
    tmp=${template//${current_var}/${current_value}}

    # Remove "%" from VAR for further use
    current_var=${current_var//%/}

    # If variable has a value then do further replacement logic
    if [ "$current_value" != "" ] ; then

        # Change "#if[VAR] " comments conditionally to uncommented lines
        tmp=${tmp//"#if[${current_var}] "/""}

        # Change #if[VAR=current_value] comments conditionally to uncommented lines
        tmp=${tmp//"#if[${current_var}=${current_value}] "/""}

        # Change remaining #if[VARIABLE=wrong_value] comments to conventional comment lines
        regex="if\[${current_var}=([^]]*)\] "
        while [[ $tmp =~ $regex ]] ; do
            tmp=${tmp//"#if[${current_var}=${BASH_REMATCH[1]}] "/"#"}
        done

        # Search for "#if[VARIABLE!=correct_value]" and enable line if found ( or transform to conventional comment lines )
        regex="if\[${current_var}!=([^]]*)\] "
        while [[ $tmp =~ $regex ]] ; do
            if [ "${BASH_REMATCH[1]}" != $current_value ] ; then
                # Change "#if[VARIABLE!=wrong_value]" comment to uncommented line
                tmp=${tmp//"#if[${current_var}!=${BASH_REMATCH[1]}] "/""}
            else
                # Change "#if[VARIABLE!=wrong_value]" comment to conventional comment line
                tmp=${tmp//"#if[${current_var}!=${BASH_REMATCH[1]}] "/"#"}
            fi
        done
    else
        # Change "#if[!VAR] " comments conditionally to uncommented lines
        tmp=${tmp//"#if[!${current_var}] "/""}

        # Change #if[VARIABLE[...]] comments to conventional comment lines
        regex="if\[${current_var}([^]]*)\] "
        while [[ $tmp =~ $regex ]] ; do
            tmp=${tmp//"#if[${current_var}${BASH_REMATCH[1]}] "/"#"}
        done
    fi

    # Set result on template var
    template=$tmp
    let COUNTER=COUNTER+1
done

echo "$template"
