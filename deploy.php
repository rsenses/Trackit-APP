<?php
namespace Deployer;

// All Deployer recipes are based on `recipe/common.php`.
// require 'recipe/common.php';
require 'recipe/common.php';

set('ssh_type', 'native');
set('ssh_multiplexing', true);

/**
 * Main task
 */
task('deploy', [
    'deploy:prepare',
    'deploy:release',
    'deploy:update_code',
    'deploy:vendors',
    'deploy:shared',
    'deploy:symlink',
    'cleanup',
])->desc('Deploy your project');

after('deploy', 'success');

// Define a server for deployment.
host('scl.prs3.expomark.es')
    ->user('deploy')
    ->identityFile('~/.ssh/do_rsa')
    // ->forwardAgent() // You can use identity key, ssh config, or username/password to auth on the server.
    ->stage('production')
    ->set('deploy_path', '/var/www/app.trackitsuite.com'); // Define the base path to deploy your project to.

// Define a server for deployment.
host('expobeta.com')
    ->user('root')
    // ->forwardAgent() // You can use identity key, ssh config, or username/password to auth on the server.
    ->stage('testing')
    ->set('deploy_path', '/var/www/app.trackitsuite.com'); // Define the base path to deploy your project to.

// Specify the repository from which to download your project's code.
// The server needs to have git installed for this to work.
// If you're not using a forward agent, then the server has to be able to clone
// your project from this repository.

set('repository', 'git@bitbucket.org:expomark/trackit-app.git');

set('shared_dirs', ['storage/logs', 'storage/cache', 'public/.well-known']);

set('shared_files', ['src/settings.php']);

set('http_user', 'www-data');

// set('composer_command', '/usr/local/bin/composer');

set('keep_releases', 2);

task('reload:server', function () {
    run('sudo service nginx reload');
    run('sudo service php7.1-fpm restart');
});

after('deploy', 'reload:server');
after('rollback', 'reload:server');
