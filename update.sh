#!/bin/bash

git pull && docker build -t sky . && docker stop sky && docker rm sky && docker run -itd --network=diy --name=sky -p 9501:9501 --restart=always sky && docker logs -f --tail=200 sky