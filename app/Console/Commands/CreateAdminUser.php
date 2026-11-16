<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

#[Signature('admin:create-user')]
#[Description('Create one active administrator account using hidden password prompts')]
class CreateAdminUser extends Command
{
    public function handle(): int
    {
        $adminRole = Role::query()->where('name', 'ADMIN')->first();

        if ($adminRole === null) {
            $this->error('Không tìm thấy role ADMIN trong database. Không có thay đổi nào được thực hiện.');

            return self::FAILURE;
        }

        $fullName = trim((string) $this->ask('Tên đầy đủ của Admin'));
        $email = Str::lower(trim((string) $this->ask('Email Admin')));
        $identityValidator = Validator::make(
            ['full_name' => $fullName, 'email' => $email],
            [
                'full_name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            ],
        );

        if ($identityValidator->fails()) {
            foreach ($identityValidator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $password = $this->secret('Mật khẩu mới (nhập ẩn)');
        $passwordConfirmation = $this->secret('Nhập lại mật khẩu (nhập ẩn)');
        $passwordValidator = Validator::make(
            ['password' => $password, 'password_confirmation' => $passwordConfirmation],
            [
                'password' => ['required', 'string', 'min:12', 'same:password_confirmation'],
                'password_confirmation' => ['required', 'string'],
            ],
            [
                'password.same' => 'Hai lần nhập mật khẩu không khớp.',
                'password.min' => 'Mật khẩu phải có ít nhất 12 ký tự.',
            ],
        );

        if ($passwordValidator->fails()) {
            foreach ($passwordValidator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $admin = User::query()->create([
            'role_id' => $adminRole->id,
            'full_name' => $fullName,
            'email' => $email,
            'password' => $password,
            'status' => 1,
        ]);

        $this->info("Đã tạo tài khoản Admin với ID {$admin->id}. Mật khẩu không được hiển thị.");

        return self::SUCCESS;
    }
}
