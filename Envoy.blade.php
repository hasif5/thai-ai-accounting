@servers(['blue' => 'deploy@blue-server.example.com', 'green' => 'deploy@green-server.example.com'])

@setup
$repository = 'git@github.com:yourusername/thai-ai-accounting.git';
$app_dir = '/var/www/thai-ai-accounting';
$release = date('YmdHis');
$new_release_dir = $app_dir . '/releases/' . $release;
@endsetup

@story('deploy')
clone_repository
run_composer
update_symlinks
run_artisan
clean_old_releases
@endstory

@task('clone_repository', ['on' => ['blue', 'green']])
echo 'Cloning repository...'
[ -d {{ $app_dir }} ] || mkdir {{ $app_dir }}
[ -d {{ $app_dir }}/releases ] || mkdir {{ $app_dir }}/releases
[ -d {{ $app_dir }}/storage ] || mkdir {{ $app_dir }}/storage

cd {{ $app_dir }}/releases
git clone --depth 1 {{ $repository }} {{ $new_release_dir }}
cd {{ $new_release_dir }}
@endtask

@task('run_composer', ['on' => ['blue', 'green']])
echo "Starting composer..."
cd {{ $new_release_dir }}
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
@endtask

@task('update_symlinks', ['on' => ['blue', 'green']])
echo "Linking storage directory..."
rm -rf {{ $new_release_dir }}/storage
ln -nfs {{ $app_dir }}/storage {{ $new_release_dir }}/storage

echo 'Linking .env file...'
ln -nfs {{ $app_dir }}/.env {{ $new_release_dir }}/.env

echo 'Linking current release...'
ln -nfs {{ $new_release_dir }} {{ $app_dir }}/current
@endtask

@task('run_artisan', ['on' => ['blue', 'green']])
echo "Starting artisan commands..."
cd {{ $new_release_dir }}
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
@endtask

@task('clean_old_releases', ['on' => ['blue', 'green']])
echo "Cleaning old releases..."
cd {{ $app_dir }}/releases
ls -dt */ | tail -n +6 | xargs -d "\n" rm -rf
@endtask

@task('health_check', ['on' => ['blue', 'green']])
echo "Performing health check..."
curl -s http://localhost/health || exit 1
@endtask

@task('nginx_switch', ['on' => ['blue', 'green']])
echo "Switching Nginx configuration..."
sudo ln -nfs {{ $app_dir }}/current /var/www/current
sudo service nginx reload
@endtask