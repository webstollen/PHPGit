<?php

namespace PHPGit\Command;

use PHPGit\Command;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Update remote refs along with associated objects - `git push`.
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class PushCommand extends Command
{
    /**
     * @see \PHPGit\Git::push()
     *
     * @param string $repository The "remote" repository that is destination of a push operation
     * @param string $refspec    Specify what destination ref to update with what source object
     * @param array  $options    An array of options
     */
    public function __invoke($repository = null, $refspec = null, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('push');

        $this->addFlags($builder, $options);

        if ($repository) {
            $builder->add($repository);

            if ($refspec) {
                $builder->add($refspec);
            }
        }

        $this->git->run($builder->getProcess());
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'all'          => false,
            'mirror'       => false,
            'tags'         => false,
            'force'        => false,
            'set-upstream' => false,
        ]);
    }
}
