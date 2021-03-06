<?php

use PHPGit\Exception\GitException;
use PHPGit\Git;

class GitExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $git = new Git();
        $git->setRepository(sys_get_temp_dir());
        try {
            $git->status();
            $this->fail('Previous operation should fail');
        } catch (GitException $e) {
            $command = $e->getProcess()->getCommandLine();
            $command = str_replace(['"', "'"], '', $command);
            $this->assertStringEndsWith('status --porcelain -s -b --null', $command);
        }
    }
}
