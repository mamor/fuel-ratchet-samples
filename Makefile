# make up
up:
	@echo 'Please manually run the following command:'
	@echo 'sudo supervisord -c fuel/packages/ratchet/supervisor.conf'

# make down
down:
	sudo supervisorctl -c fuel/packages/ratchet/supervisor.conf stop all && sudo rm /tmp/supervisor.sock

# make start-all
start-all:
	sudo supervisorctl -c fuel/packages/ratchet/supervisor.conf start all

# make stop-all
stop-all:
	sudo supervisorctl -c fuel/packages/ratchet/supervisor.conf stop all

# make restart-all
restart-all:
	sudo supervisorctl -c fuel/packages/ratchet/supervisor.conf restart all

# make status
status:
	sudo supervisorctl -c fuel/packages/ratchet/supervisor.conf status

help:
	@echo 'Commands:'
	@echo '  make up'
	@echo '  make down'
	@echo '  make start-all'
	@echo '  make stop-all'
	@echo '  make restart-all'
	@echo '  make status'
