<?php declare(strict_types=1);
/**
 * Fire Content Management System - A simple and secure piece of art.
 */

use Symfony\Component\OptionsResolver\OptionsResolver;

use function password_hash;
use function password_needs_rehash;
use function password_verify;

/**
 * The password hash class.
 */
class PasswordHash implements Core
{

    /** @var array $options The password hash options. */
    private $options = [];

    /**
     * Construct a new password hash class.
     *
     * @param array $options The password hash options.
     *
     * @return void Returns nothing.
     */
    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * Hash a new password.
     *
     * @param string $password The password to hash.
     *
     * @return string Returns the hashed password.
     */
    public function hash(string $password): string
    {
        return password_hash($this->options['algo'], $password, $this->options);
    }

    /**
     * Check to see if the hash needs a rehash.
     *
     * @param string $hash The hash to check.
     *
     * @return bool Returns true if the hash needs to be rehashed.
     */
    public function needsRehash(string $hash): bool
    {
        return password_needs_rehash();
    }

    /**
     * Verify the password matches the hash.
     *
     * @param string $password The password to check.
     * @param string $hash     The hash to check against.
     *
     * @return bool Returns true if the password matches the hash and false if not.
     */
    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Configure the password hash options.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The options resolver.
     *
     * @return void Returns nothing.
     */
    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'cost' => 10,
        ]);
        $resolver->setAllowedTypes('cost', 'int');
        $resolver->setAllowedTypes('memory_cost', 'int');
        $resolver->setAllowedTypes('time_cost', 'int');
        $resolver->setAllowedTypes('threads', 'int');
    }
}
