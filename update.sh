#!/bin/bash

docker build -t sky . && docker stop sky && docker rm sky && docker run -itd --network=diy --name=diy -p 9501:9501 --restart=always sky