<?php

namespace App\Service;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DockerService
{
    public function startContainer(string $containerName): bool
    {
        $process = new Process(['docker', 'start', $containerName]);

        try {
            $process->mustRun();
            return true;     
        } catch (ProcessFailedException $exception) {
            echo $exception->getMessage();
            return false;
        }
    }

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

    public function restartContainer(string $containerName): bool
    {
        $process = new Process(['docker', 'restart', $containerName]);

        try {
            $process->mustRun();
            return true;     
        } catch (ProcessFailedException $exception) {
            echo $exception->getMessage();
            return false;
        }
    }
}

