<?php

namespace PHPGit\Command;

use PHPGit\Command;
use PHPGit\Exception\GitException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Create an archive of files from a named tree - `git archive`.
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class ArchiveCommand extends Command
{
    /**
     * Create an archive of files from a named tree.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->archive('repo.zip', 'master', null, ['format' => 'zip']);
     * ```
     *
     * ##### Options
     *
     * - **format** (_boolean_) Format of the resulting archive: tar or zip
     * - **prefix** (_boolean_) Prepend prefix/ to each filename in the archive
     *
     * @param string                    $file    The filename
     * @param string                    $tree    [optional] The tree or commit to produce an archive for
     * @param string|array|\Traversable $path    [optional] If one or more paths are specified, only these are included
     * @param array                     $options [optional] An array of options {@see ArchiveCommand::setDefaultOptions}
     *
     * @throws GitException
     */
    public function __invoke($file, $tree = null, $path = null, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('archive');

        if ($options['format']) {
            $builder->add('--format='.$options['format']);
        }

        if ($options['prefix']) {
            $builder->add('--prefix='.$options['prefix']);
        }

        $builder->add('-o')->add($file);

        if ($tree) {
            $builder->add($tree);
        }

        if (!is_array($path) && !($path instanceof \Traversable)) {
            $path = [$path];
        }

        foreach ($path as $value) {
            $builder->add($value);
        }

        $this->git->run($builder->getProcess());
    }

    /**
     * {@inheritdoc}
     *
     * - **format** (_boolean_) Format of the resulting archive: tar or zip
     * - **prefix** (_boolean_) Prepend prefix/ to each filename in the archive
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'format' => null,
            'prefix' => null,
        ]);

        $resolver->setAllowedTypes('format', ['null', 'string'])
            ->setAllowedTypes('prefix', ['null', 'string'])
            ->setAllowedValues('format', ['tar', 'zip']);
    }
}
