#!/bin/sh
# You should install ansible for ability to run this script
# sudo apt-get install software-properties-common
# sudo apt-add-repository ppa:ansible/ansible
# sudo apt-get update
# sudo apt-get install ansible
# sudo apt-get install python-mysqldb
time ansible-playbook -vvvv reinstall.yml --tags=all --extra-vars="installation_profile_name=pp mysql_host=localhost mysql_db=ffw_intranet mysql_user=ffw_intranet mysql_pass=hyb8M73UpvBqHT4Q"
