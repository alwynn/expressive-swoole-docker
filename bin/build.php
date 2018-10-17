<?php

// get options
$options = getopt('', ['swoole::', 'proxy::', 'tag::']);
$options = array_replace([
    'tag'    => 'test/expressive',
    'swoole' => '4.2.3',
    'proxy'  => getenv('http_proxy', true),
], $options);

// function for command calls
function command(string $command, string $workdir = null) {
    if ($workdir != null) {
        $dir = getcwd();
        chdir($workdir);
    }
    $retval = 0;
    echo "\e[36m==> RUNNING: $command\e[39m\n";
    passthru($command, $retval);
    if ($workdir != null) {
        chdir($dir);
    }
    if ($retval != 0) {
        exit($retval);
    }
};

$workdir      = realpath(__DIR__ . '/..');
$buildpath    = "{$workdir}/build";
$dockerpath   = "{$workdir}/docker";
$composerpath = "{$workdir}/bin/composer.phar";
$contextpath  = "{$dockerpath}/application";

chdir($workdir);

// prepare directories
if (is_dir($buildpath)) {
    command("rm -rf $buildpath");
}
if (is_dir($contextpath)) {
    command("rm -rf $contextpath");
}
command("mkdir -p $buildpath $contextpath");

// download composer
command("wget https://getcomposer.org/composer.phar -O $composerpath");

// install expressive
command("php $composerpath create-project zendframework/zend-expressive-skeleton .", $buildpath);
// add swoole support
command("php $composerpath require zendframework/zend-expressive-swoole", $buildpath);
// ensure development mode is off
command("php $composerpath development-disable", $buildpath);

// prepare docker context
command("cp -R $buildpath/{config,public,src,vendor} $contextpath");
command("mkdir -p $buildpath/data/{cache,logs}");

// build docker image
$command = "docker build -t {$options['tag']} --build-arg swoole={$options['swoole']}";
$command .= ($options['proxy'] != false) ? " --build-arg proxy={$options['proxy']}" : '';
$command .= " .";
command($command, $dockerpath);

// cleanup
command("rm -rf $buildpath $contextpath $composerpath");

echo "\n\e[36m==> Build completed. To run the container enter:\n\e[37m$ \e[92mdocker run -dit --name test-expressive --init --restart=always -p 8080:80 {$options['tag']}\e[39m\n\n";
exit(0);
