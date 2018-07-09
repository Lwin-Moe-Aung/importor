#!/bin/bash
if [ ! -z "$2" ]; then
  encoding=$2
  supported_encoding=$(iconv -l | grep $encoding)
  if [ -z "$supported_encoding" ]; then
    encoding=""
  fi
fi
if [ -z "$encoding" ]; then
  encoding=$(file -bi "$1" | sed -e 's/.*[ ]charset=//')
fi
if [ "$encoding" == "unknown-8bit" ]; then
  iconv -c -s -t UTF-8 "$1" > "$1"_utf8
else
  iconv -c -s -f "$encoding" -t UTF-8 "$1" > "$1"_utf8
fi
awk 'NR==1{sub(/^\xef\xbb\xbf/,"")}{print}'  "$1"_utf8 > "$1"_utf8_removed_bom
mv "$1"_utf8_removed_bom "$1"
rm "$1"_utf8