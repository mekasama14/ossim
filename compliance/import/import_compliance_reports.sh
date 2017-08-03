#!/bin/bash
echo "grant all on jasperserver.* to jasperdb@127.0.0.1 identified by 'password';" | ossim-db
./js-import.sh --input-zip OSSIM-Complete-Report.zip
echo "drop user jasperdb@127.0.0.1;" | ossim-db
