<?php

namespace Talal\LabelPrinter\Mode;

class Template extends Mode
{
    /**
     * @param int       $id         The template ID
     * @param resource  $resource
     */
    public function __construct($id, $resource)
    {
        $this->setResource($resource);

        $this->setMode();
        $this->initialize();
        $this->setId($id);
    }

    /**
     * @inheritdoc
     */
    public function process()
    {
        $this->sendCommand('^FF');
    }

    /**
     * Set the printer to template mode
     *
     * @return  void
     */
    protected function setMode()
    {
        $this->sendCommand(chr(27) . 'ia3');
    }

    /**
     * Initialize template mode
     *
     * @return  void
     */
    protected function initialize()
    {
        $this->sendCommand(chr(94) . chr(73) . chr(73));

        // Set character code set to Windows-1252 (Western Europe) for umlauts.
        // ESC i X m 2 01h 00h 02h
        $this->sendCommand(chr(27) . 'iXm2' . chr(1) . chr(0) . chr(2));
    }

    /**
     * Select the template id
     *
     * @param   integer $id
     * @return  void
     */
    public function setId($id)
    {
        $n1 = intval($id / 10);
        $n2 = intval($id % 10);

        $this->sendCommand('^TS0' . strval($n1) . strval($n2));
    }
}
