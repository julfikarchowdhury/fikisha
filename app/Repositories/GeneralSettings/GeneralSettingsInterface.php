<?php
namespace App\Repositories\GeneralSettings;

interface GeneralSettingsInterface {

    public function all();
    public function update($request);
    public function getLastError(): ?string;

}
