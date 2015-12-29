#! /bin/bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

mysql -u root -p miniface < model.sql && php "${DIR}/app/dbInit.php"