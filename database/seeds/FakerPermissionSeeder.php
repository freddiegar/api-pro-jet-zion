    <?php

use App\Models\User;
use FreddieGar\Base\Constants\BlameColumn;
use FreddieGar\Rbac\Models\Permission;
use FreddieGar\Rbac\Models\Role;
use FreddieGar\Rbac\Models\RolePermission;
use Illuminate\Database\Seeder;

class FakerPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        if (!isTesting()) {
            return;
        }

        $created_by = 2;
        $granted = 1;
        $roles = [];

        $permissions = [
            'C' => 'Create',
            'R' => 'Read',
            'U' => 'Update',
            'D' => 'Delete',
            'S' => 'Show',
        ];

        $entities = [
            'Test' => 'SCRUD',
        ];

        $user = User::findOrFail($created_by);

        foreach ($entities as $entity => $perms) {
            $lenght = strlen($perms);

            Role::setCurrentUserAuthenticated($created_by);
            $role = Role::create([
                'description' => sprintf('Administration %s', $entity)
            ]);

            for ($i = 0; $i < $lenght; ++$i) {
                $letter = $perms[$i];
                $slug = strtolower(str_replace(' ', '-', sprintf('%s.%s', $entity, $permissions[$letter])));
                $description = ucfirst(strtolower(sprintf('%s %s', $permissions[$letter], $entity)));

                Permission::setCurrentUserAuthenticated($created_by);
                $permission = Permission::create(compact('slug', 'description'));

                RolePermission::setCurrentUserAuthenticated($created_by);
                RolePermission::create([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id,
                    'granted' => $granted,
                ]);
            }

            $roles[] = $role->id;

        }

        $role = Role::create([
            'description' => sprintf('Super Testing')
        ]);

        foreach ($roles as $id) {
            RolePermission::create([
                'role_id' => $role->id,
                'parent_id' => $id,
                'granted' => $granted,
            ]);
        }

        $user->roles()->attach($role->id, [BlameColumn::CREATED_BY => $created_by, BlameColumn::CREATED_AT => Carbon\Carbon::now()]);
    }
}
