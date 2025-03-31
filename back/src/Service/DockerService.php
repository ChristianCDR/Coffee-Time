<?php

namespace App\Service;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DockerService
{

    public function stopContainer(string $containerName): bool
    {
        $process = new Process(['docker', 'stop', $containerName]);

        try {
            $process->mustRun();
            return true;     
        } catch (ProcessFailedException $exception) {
            echo $exception->getMessage();
            return false;
        }
    }
}

