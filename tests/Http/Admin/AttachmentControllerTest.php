<?php

namespace App\Tests\Http\Admin;

use App\Tests\WebTestCase;

class AttachmentControllerTest extends WebTestCase
{

    public function testQueryParameterValidation(): void
    {
        $this->markTestIncomplete('Valider ?path= et ?q=');
    }

    public function testPreventDeleteIfAttachmentIsInContent(): void
    {
        $this->markTestIncomplete('Test à créer');
    }

}
