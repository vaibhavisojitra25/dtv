<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;
    public function getLogoDarkFormattedAttribute()
  {
    if (empty($this->logo_dark)) {
      return url('images/logo/logo_wide.png');
    } else {
      return asset('/images/logo') . "/" . $this->logo_dark;
    }
  }
  public function getLogoLightFormattedAttribute()
  {
    if (empty($this->logo_light)) {
      return url('images/logo_wide.png');
    } else {
      return asset('/images/logo') . "/" . $this->logo_light;
    }
  }
  public function getFaviconFormattedAttribute()
  {
    if (empty($this->favicon)) {
      return url('images/logo_wide.png');
    } else {
      return asset('/images/logo') . "/" . $this->favicon;
    }
  }
}
