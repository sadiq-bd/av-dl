FROM php:8.2-cli

ENV AVDL_DOWNLOAD_DIR=/data

COPY . /av-dl

WORKDIR /av-dl

RUN apt-get update && apt-get install -y curl tar unzip

RUN curl -L https://github.com/BtbN/FFmpeg-Builds/releases/latest/download/ffmpeg-master-latest-linux64-lgpl.tar.xz -o ffmpeg.tar.xz

RUN tar -xf ffmpeg.tar.xz && mv ffmpeg-master-latest-linux64-lgpl/bin/ffmpeg /usr/bin && rm -r ffmpeg.tar.xz ffmpeg-master-latest-linux64-lgpl

RUN curl -L https://github.com/denoland/deno/releases/latest/download/deno-x86_64-unknown-linux-gnu.zip -o deno.zip

RUN unzip deno.zip && mv deno /usr/bin && rm deno.zip

RUN curl -L https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp_linux -o /usr/bin/yt-dlp

RUN chmod +x /usr/bin/yt-dlp /usr/bin/deno /usr/bin/ffmpeg

CMD [ "php", "-S", "0.0.0.0:8080", "public/index.php" ]
