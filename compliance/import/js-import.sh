#!/bin/bash
#
# script to run import command 
#

JS_EXP_CMD_CLASS=com.jaspersoft.jasperserver.export.ImportCommand
export JS_EXP_CMD_CLASS

JS_CMD_NAME=$0
export JS_CMD_NAME

./js-import-export.sh $*
