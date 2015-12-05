<?php

/*
 * This file is part of the Yosymfony\Config-loader.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\ConfigLoader;

/**
 * Interface of operations with repositories.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface RepositoryOperationInterface
{
    /**
     * Union the repository with $repository. The values of $repository have
     * less precedence.
     *
     * @param RepositoryInterface $repository
     *
     * @return RepositoryInterface A new repository
     */
    public function union(RepositoryInterface $repository);

    /**
     * Intersection the repository with $repository. The values of $repository have
     * less precedence.
     *
     * @param RepositoryInterface $repository
     *
     * @return RepositoryInterface A new repository
     */
    public function intersection(RepositoryInterface $repository);
}
