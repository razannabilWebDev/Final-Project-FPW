<?php

namespace Tests\Unit;

use App\UserRole;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    public function test_values_follow_database_contract(): void
    {
        $this->assertSame(['admin', 'pengelola', 'staf'], UserRole::values());
    }

    public function test_labels_are_in_indonesian(): void
    {
        $this->assertSame('Admin', UserRole::Admin->label());
        $this->assertSame('Pengelola', UserRole::Pengelola->label());
        $this->assertSame('Staf Operasional', UserRole::Staf->label());
    }

    public function test_is_admin(): void
    {
        $this->assertTrue(UserRole::Admin->isAdmin());
        $this->assertFalse(UserRole::Pengelola->isAdmin());
        $this->assertFalse(UserRole::Staf->isAdmin());
    }
}
