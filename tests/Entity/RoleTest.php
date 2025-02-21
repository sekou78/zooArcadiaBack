<?php

// namespace App\Tests\Entity;

// use App\Entity\Role;
// use App\Entity\User;
// use PHPUnit\Framework\TestCase;

// class RoleTest extends TestCase
// {
//     public function testCreateRole()
//     {
//         $role = new Role();

//         // Vérification des valeurs initiales
//         $this->assertNull($role->getId());
//         $this->assertNull($role->getLabel());
//         $this->assertEmpty($role->getUsers());
//         $this->assertNull($role->getCreatedAt());
//         $this->assertNull($role->getUpdatedAt());
//     }

//     public function testSetAndGetLabel()
//     {
//         $role = new Role();
//         $label = "ROLE_ADMIN";
//         $role->setLabel($label);

//         $this->assertEquals(
//             $label,
//             $role->getLabel()
//         );
//     }

//     public function testAddAndRemoveUser()
//     {
//         $role = new Role();
//         $user = new User();

//         // Ajouter un utilisateur
//         $role->addUser($user);
//         $this->assertCount(
//             1,
//             $role->getUsers()
//         );
//         $this->assertSame(
//             $role,
//             $user->getRole()
//         );

//         // Supprimer un utilisateur
//         $role->removeUser($user);
//         $this->assertCount(
//             0,
//             $role->getUsers()
//         );
//         $this->assertNull(
//             $user->getRole()
//         );
//     }

//     public function testSetAndGetCreatedAt()
//     {
//         $role = new Role();
//         $date = new \DateTimeImmutable();
//         $role->setCreatedAt($date);

//         $this->assertSame(
//             $date,
//             $role->getCreatedAt()
//         );
//     }

//     public function testSetAndGetUpdatedAt()
//     {
//         $role = new Role();
//         $date = new \DateTimeImmutable();
//         $role->setUpdatedAt($date);

//         $this->assertSame(
//             $date,
//             $role->getUpdatedAt()
//         );
//     }
// }
