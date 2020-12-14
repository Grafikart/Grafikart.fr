alias statuswatch "watch -c SYSTEMD_COLORS=1 systemctl status"
set -x PATH $HOME/bin $PATH
set -x DOCKER_HOST unix:///run/user/1000/docker.sock
