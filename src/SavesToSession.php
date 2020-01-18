<?php

namespace DarkGhostHunter\Laratraits;

use LogicException;
use Illuminate\Contracts\Session\Session;

trait SavesToSession
{
    /**
     * Saves the current object (or a part of it) to session.
     *
     * @param  string|null  $key
     */
    public function toSession(string $key = null)
    {
        app(Session::class)->put($key ?? $this->sessionKey(), $this->sessionValue());
    }

    /**
     * The value to insert into the Session.
     *
     * @return $this
     */
    protected function sessionValue()
    {
        return $this;
    }

    /**
     * The key name to use in the session.
     *
     * @return string
     */
    protected function sessionKey()
    {
        throw new LogicException('The class ' . class_basename($this) . ' has no default session key.');
    }
}
