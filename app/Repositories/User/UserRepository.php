<?php

namespace App\Repositories\User;

use App\Enums\Status;
use App\Models\User;
use App\Models\Backend\Department;
use App\Models\Backend\Designation;
use App\Models\Backend\Upload;
use Illuminate\Support\Facades\Hash;
use App\Repositories\User\UserInterface;
use App\Enums\UserType;
use App\Models\Backend\Role;

class UserRepository implements UserInterface
{

    // get all rows in User model with Upload model row same as foreign key.
    public function all()
    {
        return User::where('user_type', UserType::ADMIN)->with('upload')->orderByDesc('id')->paginate(10);
    }

    public function LeaveApplyUser()
    {
        return User::where('user_type', UserType::ADMIN)->where('status', Status::ACTIVE)->whereNot('id', 1)->orderByDesc('id')->get();
    }

    public function adminUsers()
    {
        return User::where('user_type', UserType::ADMIN)->where('status', Status::ACTIVE)->orderByDesc('id')->get();
    }

    public function attendanceUser()
    {
        return User::where('user_type', UserType::ADMIN)->where('status', Status::ACTIVE)->orderByDesc('id')->paginate(10);
    }

    public function getFilterAttendanceUsers($request)
    {
        return User::where(function ($query) use ($request) {
            if (!empty($request->search)) :
                $query->where('name', 'like', '%' . $request->search . '%');
                $query->orWhere('email', 'like', '%' . $request->search . '%');
            endif;
        })->where(['user_type' => UserType::ADMIN, 'status' => Status::ACTIVE])->orderByDesc('id')->paginate(10, '*', 'summery');
    }

    public function filter($request)
    {
        return User::where(function ($query) use ($request) {
            if ($request->name) {
                $query->where('name', 'like', '%' . $request->name . '%');
            }
            if ($request->email) {
                $query->where('email', 'like', '%' . $request->email . '%');
            }
            if ($request->phone) :
                $query->where('mobile', 'like', '%' . $request->phone . '%');
            endif;
        })->orderByDesc('id')->paginate(10);
    }

    // get all rows in Department model
    public function departments()
    {
        return Department::active()->orderBy('title')->get();
    }

    // get all rows in Designation model
    public function designations()
    {
        return Designation::active()->orderBy('title')->get();
    }

    // get single row in User model with Upload model row same as foreign key.
    public function get($id)
    {
        return User::with('upload', 'role')->find($id);
    }

    // All request data store in User tabel.
    public function store($request)
    {
        try {
            $role                   = Role::where('id', $request->role_id)->first();
            $user                   = new User();
            $user->name             = $request->name;
            $user->email            = $request->email;
            $user->password         = Hash::make($request->password);
            $user->country_code           = $request->country_code;
            $user->mobile           = $request->mobile;
            $user->nid_number       = $request->nid_number;
            $user->designation_id   = $request->designation_id;
            $user->department_id    = $request->department_id;
            $user->image_id         = $this->file($request->image, '');
            $user->joining_date     = $request->joining_date;
            $user->address          = $request->address;
            $user->role_id          = $request->role_id;
            $user->salary           = $request->salary !== "" ? $request->salary : 0;
            $user->per_hour_salary  = $request->per_hour_salary !== "" ? $request->per_hour_salary : 0;
            $user->permissions     = $role->permissions;
            $user->status           = $request->status;
            $user->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // All request data update in User tabel.
    public function update($id, $request)
    {
        try {
            $role = Role::where('id', $request->role_id)->first();
            $user                       = User::find($id);
            $user->name                 = $request->name;
            $user->email                = $request->email;
            $user->country_code           = $request->country_code;
            $user->mobile               = $request->mobile;
            $user->nid_number           = $request->nid_number;
            if ($id != 1) {
                $user->designation_id   = $request->designation_id;
                $user->department_id    = $request->department_id;
                $user->status           = $request->status;
            }
            $user->joining_date         = $request->joining_date;
            $user->address              = $request->address;
            if ($request->password != null) {
                $user->password = Hash::make($request->password);
            }
            if (isset($request->image) && $request->image != null) {
                $user->image_id = $this->file($request->image, $user->image_id);
            }
            $user->role_id              = $request->role_id;
            $user->salary               = $request->salary !== "" ? $request->salary : 0;
            $user->per_hour_salary     = $request->per_hour_salary !== "" ? $request->per_hour_salary : 0;
            $user->permissions     = $role->permissions;
            $user->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // Delete single row in User Model with Delete single row in Upload model and delete image in public/upload/user folder..
    public function delete($id)
    {
        try {
            if ($id != 1) {
                $user = User::with('upload')->find($id);
                Upload::destroy($user->upload->id);
                if (file_exists(public_path($user->upload->original)))
                    unlink(public_path($user->upload->original));
                $user->delete();
                return 1;
            } else
                return 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    // Request image Store in Upload Model and image copy file attach in public/upload/user folder.
    public function file($image, $image_id = '')
    {
        try {
            $image_name = '';
            if (!blank($image)) {
                $destinationPath       = public_path('uploads/users');
                $profileImage          = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $profileImage);
                $image_name            = 'uploads/users/' . $profileImage;
            }
            if (blank($image_id)) {
                $upload           = new Upload();
            } else {
                $upload           = Upload::find($image_id);
                if (file_exists(public_path($upload->original))) {
                    unlink(public_path($upload->original));
                }
            }
            $upload->original     = $image_name;
            $upload->save();
            return $upload->id;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function permissionUpdate($id, $request)
    {

        try {
            $user = User::where('id', $id)->first();
            if ($request->permissions !== null) {
                $user->permissions = $request->permissions;
            } else {
                $user->permissions = [];
            }
            $user->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
