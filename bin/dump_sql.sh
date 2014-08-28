#!/bin/bash

DEST=/home/bloodlines/public_html

pg_dump i3log2 >$DEST/i3log_dump_new.sql
bzip2 -9 $DEST/i3log_dump_new.sql
mv -f $DEST/i3log_dump_new.sql.bz2 $DEST/i3log_dump.sql.bz2

