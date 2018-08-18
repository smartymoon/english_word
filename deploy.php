<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'tools');

// Project repository
set('repository', 'git@gitee.com:littlemoon/tools.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', []);
set('allow_anonymous_stats', false);

// Hosts

host('tools.tt12t.com')
    ->user('lee')
    ->identityFile('~/.ssh/lee_rsa')
    ->set('deploy_path', '/var/www/{{application}}');    
    
// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

// restart-server
task('restart-server', function() {
        run('sudo /usr/local/nginx/sbin/nginx -s reload');
	run('sudo pkill -USR2 php-fpm');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'artisan:migrate');

after('cleanup', 'restart-server');

