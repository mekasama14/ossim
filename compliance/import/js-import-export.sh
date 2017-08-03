#!/bin/bash
#
# script to run export-import command
#

#
# Collect the command line args
#

CMD_LINE_ARGS=$*

#
# add config dir to class path
#
CONFIG_DIR=config
EXP_CLASSPATH="$EXP_CLASSPATH:$CONFIG_DIR"
 
#
# Loop through and add all class jars
#
# Jars are in ../jasperserver/WEB-INF/lib or lib
#

if test -d lib
then
    for i in lib/*.jar
    do
        EXP_CLASSPATH="$EXP_CLASSPATH:$i"
    done

else

    for i in ../jasperserver/WEB-INF/lib/*.jar
    do
        EXP_CLASSPATH="$EXP_CLASSPATH:$i"
    done

fi

#
# Loop through and add all spring config xmls
#
JS_EXPORT_IMPORT_CONFIG=
for i in $CONFIG_DIR/applicationContext*.xml
do
    if [ -z "$JS_EXPORT_IMPORT_CONFIG" ]
    then
        JS_EXPORT_IMPORT_CONFIG=$i
    else
        JS_EXPORT_IMPORT_CONFIG=$JS_EXPORT_IMPORT_CONFIG,$i
    fi
done

#
# Locate the java binary bundled with installer
#
# If "../java/bin/java" exists, use it
#

JAVA_EXEC=java

if test -f ../java/bin/java
then
    JAVA_HOME=../java
    PATH=$JAVA_HOME/bin:$PATH
    JAVA_EXEC=$JAVA_HOME/bin/java
fi

# run java
$JAVA_EXEC -cp "$EXP_CLASSPATH" $JS_EXP_CMD_CLASS $JS_CMD_NAME --configResources $JS_EXPORT_IMPORT_CONFIG $CMD_LINE_ARGS
