<?php

namespace PHPGit\Command;

use PHPGit\Command;
use PHPGit\Exception\GitException;
use PHPGit\Model\Log;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Show commit logs - `git log`.
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class LogCommand extends Command
{
    /**
     * Returns the commit logs.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $logs = $git->log(array('limit' => 10));
     * ```
     *
     * ##### Output Example
     *
     * ``` php
     * [
     *     0 => [
     *         'hash'  => '1a821f3f8483747fd045eb1f5a31c3cc3063b02b',
     *         'name'  => 'John Doe',
     *         'email' => 'john@example.com',
     *         'date'  => 'Fri Jan 17 16:32:49 2014 +0900',
     *         'title' => 'Initial Commit'
     *     ],
     *     1 => [
     *         //...
     *     ]
     * ]
     * ```
     *
     * ##### Options
     *
     * - **limit**            (_integer_) Limits the number of commits to show
     * - **skip**             (_integer_) Skip number commits before starting to show the commit output
     * - **grep**             (_integer_) Limit the commits output to ones with log message that matches the specified pattern (regular expression)
     * - **extended-regexp**  (_bool_)    Consider the limiting patterns to be extended regular expressions instead of the default basic regular expressions
     * - **no-merges**        (_bool_)    Consider the limiting patterns to be extended regular expressions instead of the default basic regular expressions
     * - **reverse**          (_bool_)    Reverse the order of the commits
     *
     * @param string $revRange [optional] Show only commits in the specified revision range
     * @param string $path     [optional] Show only commits that are enough to explain how the files that match the specified paths came to be
     * @param array  $options  [optional] An array of options {@see LogCommand::setDefaultOptions}
     *
     * @throws GitException
     *
     * @return Log[]
     */
    public function __invoke($revRange = '', $path = null, array $options = [])
    {
        $commits = [];
        $options = $this->resolve($options);

        $builder = $this->git->getProcessBuilder()
            ->add('log')
            ->add('-n')->add($options['limit'])
            ->add('--skip='.$options['skip'])
            ->add('--format=%H||%aN||%aE||%aD||%s');

        $this->addFlags($builder, $options, ['extended-regexp', 'no-merges', 'reverse']);

        if ($options['grep']) {
            $builder->add('--grep='.$options['grep']);
        }

        if ($revRange) {
            $builder->add($revRange);
        }

        if ($path) {
            $builder->add('--')->add($path);
        }

        $output = $this->git->run($builder->getProcess());
        $lines  = $this->split($output);

        foreach ($lines as $line) {
            list($hash, $name, $email, $date, $title) = preg_split('/\|\|/', $line, -1, PREG_SPLIT_NO_EMPTY);

            $commits[] = new Log($name, $email, $date, $title, $hash);
        }

        return $commits;
    }

    /**
     * {@inheritdoc}
     *
     * - **limit** (_integer_) Limits the number of commits to show
     * - **skip**  (_integer_) Skip number commits before starting to show the commit output
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'limit'           => 1000,
            'skip'            => 0,
            'grep'            => null,
            'extended-regexp' => false,
            'no-merges'       => false,
            'reverse'         => false,
        ]);
    }
}
