#!/bin/bash

# Script takes the following parameters:
# [--acl-all-networks] - Add all container's network in the PURGE ACL.
# [--acl-add ...] - Add a host or network segment to the PURGE ACL

function create_template_file
{
    if [ -f /etc/varnish/parameters.vcl.template ]; then
        cp /etc/varnish/parameters.vcl.template /etc/varnish/parameters.vcl
    else
        cp /etc/varnish/parameters.vcl /etc/varnish/parameters.vcl.template
    fi
}

function get_net_segments
{
    for IP_ADDR in `hostname -I`; do
        IFS=. read -r io1 io2 io3 io4 <<< "$IP_ADDR"
        IFS=. read -r mo1 mo2 mo3 mo4 mo5 < <(ifconfig -a | sed -n "/inet $IP_ADDR /{ s/.*netmask \(.*\) broadcast.*/\1/;p; }")
        if [ "$mo1" == "" ]; then
            continue;
        fi
        mb1=$(echo "obase=2;$mo1"|bc)
        mb2=$(echo "obase=2;$mo2"|bc)
        mb3=$(echo "obase=2;$mo3"|bc)
        mb4=$(echo "obase=2;$mo4"|bc)

        NETMASK=`echo $mb1 $mb2 $mb3 $mb4|tr -cd '1' | wc -c`
        NET_ADDR="$((io1 & mo1)).$(($io2 & mo2)).$((io3 & mo3)).$((io4 & mo4))"

        echo $NET_ADDR/$NETMASK
    done
}

# $1 is segment, format 1.2.3.4/24 or myhostname
function add_segment
{
    # convert format 1.2.3.4/24 --> "1.2.3.4"/24;
    segment=`echo $1 | sed "s|\(.*\)/\(.*\)|\"\1\"/\2;|"`

    # convert format myhost --> "myhost";  ( any string not containing slash )
    segment=`echo $segment | sed -E "s|^([^/]+)\$|\"\1\";|"`

    echo "Adding network segment to varnish ACL : $segment"
    sed -i -s "s|\(.*ACL_INVALIDATOR.*\)|    $segment\n\1|" /etc/varnish/parameters.vcl
}

create_template_file

while (( "$#" )); do
    if [ "$1" = "--acl-all-networks" ]; then
        segments=`get_net_segments`

        for segment in `echo $segments`; do
            add_segment $segment
        done
    elif [ "$1" = "--acl-add" ]; then
        shift
        new_network="$1"

        if [ "$new_network" = "" ]; then
            echo "Warning : --acl-add parameter needs to be followed by a network segment, for instance \"--acl-add 10.0.1.0/24\""
        else
            add_segment $new_network
        fi
    else
        echo "Warning : Unrecognized parameter $1"
    fi

    shift
done

varnishd -F -a :80 -T :6082 -f /etc/varnish/default.vcl -s malloc,${VARNISH_MALLOC_SIZE}
