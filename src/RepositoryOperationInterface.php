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
     * Performs the union between the current repository and the repository
     * passed as argument. The repository passed as argument has less precedence.
     *
     * @param RepositoryInterface $repository
     *
     * @return RepositoryInterface A new repository
     */
    public function union(RepositoryInterface $repository);

    /**
     * Performs the intersection between the current repository and the repository
     * passed as argument. The repository passed as argument has less precedence.
     *
     * @param RepositoryInterface $repository
     *
     * @return RepositoryInterface A new repository
     */
    public function intersection(RepositoryInterface $repository);
}
