drupal project
======
Welcome to the d8intranet!

Requirements: 
1. Ansible 
2. Vagrant & VirtualBox 
3. Useful Vagrant plugins: HostsUpdater, VBGuest 
4. npm -g yo gulp bower generator-gulp-angular generator-angular

To setup project locally: 
1. git clone git@github.com:propeopleua/d8intranet.git 
2. cd d8intranet 
3. vagrant up 
4. Angular - cd angular && gulp serve

Drupal project is for SPS


Linux Containers
=====

When your system enpowered with linux containers(lxc), you can speedup a lot of things by
using them and getting rid of virtualization.
For approaching lxc, please install vagrant plugin

```sh
vagrant plugin install vagrant-lxc
apt-get install redir lxc cgroup-bin
```

When your system is enpowered by apparmor, you should enable nfs mounts for your host
machine
Do that by editing ```/etc/apparmor.d/lxc/lxc-default``` file with one line

```ruby
profile lxc-container-default flags=(attach_disconnected,mediate_deleted) {
  ...
    mount options=(rw, bind, ro),
  ...
```
and reload apparmor service
```sh
sudo /etc/init.d/apparmor reload
```


and run the box by command

```sh
VAGRANT_CI=yes vagrant up
```

Do use 
```
VAGRANT_CI=yes
```
environment variable, if you got issues with all vagrant commands.
