<?php

namespace App\Models\Backend\FrontWeb;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    /** CMS pages that must always exist and cannot be deleted. */
    public const SYSTEM_PAGE_SLUGS = ['privacy_policy', 'terms_conditions'];

    public static function isSystemPage(?string $pageSlug): bool
    {
        return in_array($pageSlug, self::SYSTEM_PAGE_SLUGS, true);
    }

    protected static function booted(): void
    {
        static::deleting(function (Page $page) {
            if (self::isSystemPage($page->page)) {
                return false;
            }
        });
    }

    public function scopeActive($query){
        return $query->where('status',Status::ACTIVE);
    }
    public function getMyStatusAttribute(){
        if($this->status == Status::ACTIVE):
            return '<span class="badge badge-success">'.__('status.'.$this->status).'</span>';
        else:
            return '<span class="badge badge-danger">'.__('status.'.$this->status).'</span>';
        endif;
    }
}
