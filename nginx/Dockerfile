ARG NGINX_VERSION=1.27.0

FROM nginx:${NGINX_VERSION}-perl

SHELL ["/bin/bash", "-i", "-c"]

COPY nginx.conf /etc/nginx/nginx.conf

ARG SITE_EDITOR_GID=830
ARG DOCKER_USER_UID_GID=1100
ARG DOCKER_WEB_USER=101
ARG NGINX_PORT=8080
ARG NGINX_HOST=0.0.0.0
ARG PROJECT_ROOT
ARG PUBLIC_ROOT

ENV NGINX_PORT ${NGINX_PORT}
ENV NGINX_HOST ${NGINX_HOST}
ENV PROJECT_ROOT ${PROJECT_ROOT}
ENV PUBLIC_ROOT ${PUBLIC_ROOT}

RUN groupadd -g ${SITE_EDITOR_GID} site_editor && \
	useradd -Um -u ${DOCKER_USER_UID_GID} -G site_editor docker_user && \
	usermod -a -G site_editor `id -un ${DOCKER_WEB_USER}`

COPY templates /etc/nginx/templates

RUN /docker-entrypoint.d/20-envsubst-on-templates.sh

USER nginx