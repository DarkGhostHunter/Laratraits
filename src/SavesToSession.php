<?php

namespace DarkGhostHunter\Laratraits;

use LogicException;
use JsonSerializable;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Htmlable;

trait SavesToSession
{
    /**
     * Saves the current object (or a part of it) to session.
     *
     * @param  string|null  $key
     * @return void
     */
    public function saveToSession(string $key = null)
    {
        app(Session::class)->put($key ?? $this->defaultSessionKey(), $this->toSession());
    }

    /**
     * The key name to use in the session.
     *
     * @return string
     */
    protected function defaultSessionKey()
    {
        throw new LogicException('The class ' . class_basename($this) . ' has no default session key.');
    }

    /**
     * The value to insert into the Session.
     *
     * @return string|$this
     */
    protected function toSession()
    {
        if ($this instanceof Jsonable) {
            return $this->toJson();
        }

        if ($this instanceof JsonSerializable) {
            return json_encode($this);
        }

        if ($this instanceof Htmlable) {
            return $this->toHtml();
        }

        if (method_exists($this, '__toString')) {
            return $this->__toString();
        }

        return $this;
    }
}
