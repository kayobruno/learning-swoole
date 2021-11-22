#!/bin/bash

ENDPOINT="http://localhost:8080/send-message"

MESSAGE="KISO0234000700810822200000200000004000000000000001103091310239410110300301"

function sendCurl() {
    curl -X POST "${ENDPOINT}" -H 'Content-Type: application/json' -d "{\"message\":\"${1}\"}"
}

while true
do
  sendCurl ${MESSAGE}

  sendCurl ${MESSAGE}

  sendCurl ${MESSAGE}

  sendCurl ${MESSAGE}

  sendCurl ${MESSAGE}

  sendCurl ${MESSAGE}

  sendCurl ${MESSAGE}

  sendCurl ${MESSAGE}

  sleep 1
done