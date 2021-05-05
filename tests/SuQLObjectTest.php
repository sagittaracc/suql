<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use suql\core\SuQLObject;
use suql\core\SuQLScheme;
use suql\builder\SQLDriver;
use suql\core\SuQLSimpleParam;

final class SuQLObjectTest extends TestCase
{
    private $osuql;

    protected function setUp(): void
    {
        $scheme = new SuQLScheme();
        $scheme->rel('users', 'user_group', 'users.id = user_group.user_id');
        $scheme->rel('user_group', 'groups', 'user_group.group_id = groups.id');

        $driver = new SQLDriver('mysql');

        $this->osuql = new SuQLObject($scheme, $driver);
    }

    protected function tearDown(): void
    {
        $this->osuql = null;
    }

    public function testSelectAll(): void
    {
        $sql =
            'select '.
                '* '.
            'from users';

        $this->osuql->addSelect('select_all');
        $this->osuql->getQuery('select_all')->addFrom('users');
        $suql = $this->osuql->getSQL(['select_all']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_all']));
    }

    public function testSelectAllWithTableName(): void
    {
        $sql =
            'select '.
                'users.* '.
            'from users';

        $this->osuql->addSelect('select_all_with_table_name');
        $this->osuql->getQuery('select_all_with_table_name')->addFrom('users');
        $this->osuql->getQuery('select_all_with_table_name')->addField('users', '*');
        $suql = $this->osuql->getSQL(['select_all_with_table_name']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_all_with_table_name']));
    }

    public function testSelectFieldList(): void
    {
        $sql =
            'select '.
                'users.id, '.
                'users.name '.
            'from users';

        $this->osuql->addSelect('select_field_list');
        $this->osuql->getQuery('select_field_list')->addFrom('users');
        $this->osuql->getQuery('select_field_list')->addField('users', 'id');
        $this->osuql->getQuery('select_field_list')->addField('users', 'name');
        $suql = $this->osuql->getSQL(['select_field_list']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_field_list']));
    }

    public function testSelectUsingAliases(): void
    {
        $sql =
            'select '.
                'users.id as uid, '.
                'users.name as uname '.
            'from users';

        $this->osuql->addSelect('select_using_aliases');
        $this->osuql->getQuery('select_using_aliases')->addFrom('users');
        $this->osuql->getQuery('select_using_aliases')->addField('users', ['id' => 'uid']);
        $this->osuql->getQuery('select_using_aliases')->addField('users', 'name@uname'); // just another way to set an alias
        $suql = $this->osuql->getSQL(['select_using_aliases']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_using_aliases']));
    }

    public function testSelectRaw(): void
    {
        $sql = "select 2 * 2, 'Yuriy' as author";

        $this->osuql->addSelect('select_raw');
        $this->osuql->getQuery('select_raw')->addField(null, "2 * 2");
        $this->osuql->getQuery('select_raw')->addField(null, "'Yuriy' as author");
        $suql = $this->osuql->getSQL(['select_raw']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_raw']));
    }

    public function testSelectLimit(): void
    {
        $sql =
            'select '.
                'users.* '.
            'from users '.
            'limit 3';

        $this->osuql->addSelect('select_limit');
        $this->osuql->getQuery('select_limit')->addFrom('users');
        $this->osuql->getQuery('select_limit')->addField('users', '*');
        $this->osuql->getQuery('select_limit')->addOffset(0);
        $this->osuql->getQuery('select_limit')->addLimit(3);
        $suql = $this->osuql->getSQL(['select_limit']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_limit']));
    }

    public function testSelectOffsetLimit(): void
    {
        $sql =
            'select '.
                'users.* '.
            'from users '.
            'limit 3, 3';

        $this->osuql->addSelect('select_offset_limit');
        $this->osuql->getQuery('select_offset_limit')->addFrom('users');
        $this->osuql->getQuery('select_offset_limit')->addField('users', '*');
        $this->osuql->getQuery('select_offset_limit')->addOffset(3);
        $this->osuql->getQuery('select_offset_limit')->addLimit(3);
        $suql = $this->osuql->getSQL(['select_offset_limit']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_offset_limit']));
    }

    public function testSelectDistinct(): void
    {
        $sql =
            'select distinct '.
                'users.name '.
            'from users';

        $this->osuql->addSelect('select_distinct');
        $this->osuql->getQuery('select_distinct')->addModifier('distinct');
        $this->osuql->getQuery('select_distinct')->addField('users', 'name');
        $this->osuql->getQuery('select_distinct')->addFrom('users');
        $suql = $this->osuql->getSQL(['select_distinct']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_distinct']));
    }

    public function testStoredFunction(): void
    {
        $sql = "select some_func(1,false,'Yuriy',NULL)";

        $this->osuql->addFunction('stored_function', 'some_func');
        $this->osuql->getQuery('stored_function')->addParams([1, false, 'Yuriy', null]);
        $suql = $this->osuql->getSQL(['stored_function']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['stored_function']));
    }

    public function testStoredProcedure(): void
    {
        $sql = "call some_proc(1,false,'Yuriy',NULL)";

        $this->osuql->addProcedure('stored_procedure', 'some_proc');
        $this->osuql->getQuery('stored_procedure')->addParams([1, false, 'Yuriy', null]);
        $suql = $this->osuql->getSQL(['stored_procedure']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['stored_procedure']));
    }

    public function testStrictWhere(): void
    {
        $sql =
            'select '.
                'users.id as uid, '.
                'users.name as uname '.
            'from users '.
            'where users.id % 2 = 0';

        $this->osuql->addSelect('strict_where');
        $this->osuql->getQuery('strict_where')->addFrom('users');
        $this->osuql->getQuery('strict_where')->addField('users', ['id' => 'uid']);
        $this->osuql->getQuery('strict_where')->addField('users', ['name' => 'uname']);
        $this->osuql->getQuery('strict_where')->addWhere('uid % 2 = 0');
        $suql = $this->osuql->getSQL(['strict_where']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['strict_where']));
    }

    public function testSelectWhereSubQuery(): void
    {
        $sql =
            'select '.
                'users.id as uid, '.
                'users.name '.
            'from users '.
            'where users.id not in ('.
                'select distinct '.
                    'user_group.user_id '.
                'from user_group'.
            ')';

        $this->osuql->addSelect('main_query');
        $this->osuql->getQuery('main_query')->addFrom('users');
        $this->osuql->getQuery('main_query')->addField('users', 'id@uid');
        $this->osuql->getQuery('main_query')->addField('users', 'name');
        $this->osuql->getQuery('main_query')->addWhere('uid not in @sub_query_users_belong_to_any_group');

        $this->osuql->addSelect('sub_query_users_belong_to_any_group');
        $this->osuql->getQuery('sub_query_users_belong_to_any_group')->addModifier('distinct');
        $this->osuql->getQuery('sub_query_users_belong_to_any_group')->addFrom('user_group');
        $this->osuql->getQuery('sub_query_users_belong_to_any_group')->addField('user_group', 'user_id');

        $suql = $this->osuql->getSQL(['main_query']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['main_query']));
    }

    public function testSelectJoin(): void
    {
        $this->osuql->addSelect('main');
        $this->osuql->getQuery('main')->addFrom('users');
        $this->osuql->getQuery('main')->addJoin('inner', 'user_group');
        $this->osuql->getQuery('main')->addJoin('inner', 'groups');
        $this->osuql->getQuery('main')->addField('groups', 'id@gid');
        $this->osuql->getQuery('main')->addField('groups', 'name@gname');
        $this->assertEquals(
            $this->osuql->getSQL('all'),
            'select ' .
                'groups.id as gid, ' .
                'groups.name as gname ' .
                'from users ' .
                'inner join user_group on users.id = user_group.user_id ' .
                'inner join groups on user_group.group_id = groups.id'
        );
        $this->assertNull($this->osuql->getSQL('all'));

        // join and where
        $this->osuql->addSelect('main');
        $this->osuql->getQuery('main')->addFrom('users');
        $this->osuql->getQuery('main')->addField('users', 'id');
        $this->osuql->getQuery('main')->addField('users', 'registration');
        $this->osuql->getQuery('main')->addJoin('inner', 'user_group');
        $this->osuql->getQuery('main')->addJoin('inner', 'groups');
        $this->osuql->getQuery('main')->addField('groups', ['name' => 'group']);
        $this->osuql->getQuery('main')->addWhere("group = 'admin'");
        $this->assertEquals(
            $this->osuql->getSQL('all'),
            'select ' .
                'users.id, ' .
                'users.registration, ' .
                'groups.name as group ' .
                'from users ' .
                'inner join user_group on users.id = user_group.user_id ' .
                'inner join groups on user_group.group_id = groups.id ' .
                'where groups.name = \'admin\''
        );
        $this->assertNull($this->osuql->getSQL('all'));

        $this->osuql->getScheme()->rel(['users' => 'u'], ['view' => 'v'], 'u.id = v.id');
        $this->osuql->addSelect('main');
        $this->osuql->getQuery('main')->addFrom('users');
        $this->osuql->getQuery('main')->addField('users', 'id');
        $this->osuql->getQuery('main')->addJoin('inner', 'view');
        $this->osuql->addSelect('view');
        $this->osuql->getQuery('view')->addFrom('users');
        $this->osuql->getQuery('view')->addField('users', 'id');
        $this->assertEquals(
            $this->osuql->getSQL(['main']),
            'select ' .
                'users.id ' .
                'from users ' .
                'inner join (' .
                'select ' .
                'users.id ' .
                'from users' .
                ') view on users.id = view.id'
        );
    }

    public function testSelectGroup(): void
    {
        $this->osuql->addSelect('main');
        $this->osuql->getQuery('main')->addFrom('users');
        $this->osuql->getQuery('main')->addJoin('inner', 'user_group');
        $this->osuql->getQuery('main')->addJoin('inner', 'groups');
        $this->osuql->getQuery('main')->addField('groups', 'name@gname');
        $this->osuql->getQuery('main')->addField('groups', 'name@count');
        $this->osuql->getQuery('main')->getField('groups', 'name@count')->addModifier('group');
        $this->osuql->getQuery('main')->getField('groups', 'name@count')->addModifier('count');
        $this->osuql->getQuery('main')->addWhere("gname = 'admin'");
        $this->assertEquals(
            $this->osuql->getSQL('all'),
            'select ' .
                'groups.name as gname, ' .
                'count(groups.name) as count ' .
                'from users ' .
                'inner join user_group on users.id = user_group.user_id ' .
                'inner join groups on user_group.group_id = groups.id ' .
                'where groups.name = \'admin\' ' .
                'group by groups.name'
        );
        $this->assertNull($this->osuql->getSQL('all'));
    }

    public function testNestedQueries(): void
    {
        $this->osuql->addSelect('allGroupCount');
        $this->osuql->getQuery('allGroupCount')->addFrom('users');
        $this->osuql->getQuery('allGroupCount')->addJoin('inner', 'user_group');
        $this->osuql->getQuery('allGroupCount')->addJoin('inner', 'groups');
        $this->osuql->getQuery('allGroupCount')->addField('groups', 'name@gname');
        $this->osuql->getQuery('allGroupCount')->addField('groups', 'name@count');
        $this->osuql->getQuery('allGroupCount')->getField('groups', 'name@count')->addModifier('group');
        $this->osuql->getQuery('allGroupCount')->getField('groups', 'name@count')->addModifier('count');
        $this->osuql->addSelect('main');
        $this->osuql->getQuery('main')->addFrom('allGroupCount');
        $this->osuql->getQuery('main')->addField('allGroupCount', 'gname');
        $this->osuql->getQuery('main')->addField('allGroupCount', 'count');
        $this->osuql->getQuery('main')->addWhere("gname = 'admin'");
        $this->assertEquals(
            $this->osuql->getSQL(['main']),
            'select ' .
                'allGroupCount.gname, ' .
                'allGroupCount.count ' .
                'from (' .
                'select ' .
                'groups.name as gname, ' .
                'count(groups.name) as count ' .
                'from users ' .
                'inner join user_group on users.id = user_group.user_id ' .
                'inner join groups on user_group.group_id = groups.id ' .
                'group by groups.name' .
                ') allGroupCount ' .
                'where gname = \'admin\''
        );
        $this->assertNull($this->osuql->getSQL('all'));
    }

    public function testSorting(): void
    {
        $this->osuql->addSelect('main');
        $this->osuql->getQuery('main')->addFrom('users');
        $this->osuql->getQuery('main')->addJoin('inner', 'user_group');
        $this->osuql->getQuery('main')->addJoin('inner', 'groups');
        $this->osuql->getQuery('main')->addField('groups', 'name@gname');
        $this->osuql->getQuery('main')->addField('groups', 'name@count');
        $this->osuql->getQuery('main')->getField('groups', 'name@count')->addModifier('group');
        $this->osuql->getQuery('main')->getField('groups', 'name@count')->addModifier('count');
        $this->osuql->getQuery('main')->getField('groups', 'name@count')->addModifier('asc');
        $this->assertEquals(
            $this->osuql->getSQL('all'),
            'select ' .
                'groups.name as gname, ' .
                'count(groups.name) as count ' .
                'from users ' .
                'inner join user_group on users.id = user_group.user_id ' .
                'inner join groups on user_group.group_id = groups.id ' .
                'group by groups.name ' .
                'order by count asc'
        );
        $this->assertNull($this->osuql->getSQL('all'));
    }

    public function testUnion(): void
    {
        $this->osuql->addSelect('firstRegisration');
        $this->osuql->getQuery('firstRegisration')->addFrom('users');
        $this->osuql->getQuery('firstRegisration')->addField('users', 'registration@reg_interval');
        $this->osuql->getQuery('firstRegisration')->getField('users', 'registration@reg_interval')->addModifier('min');
        $this->osuql->addSelect('lastRegisration');
        $this->osuql->getQuery('lastRegisration')->addFrom('users');
        $this->osuql->getQuery('lastRegisration')->addField('users', 'registration@reg_interval');
        $this->osuql->getQuery('lastRegisration')->getField('users', 'registration@reg_interval')->addModifier('max');
        $this->osuql->addUnion('main', '@firstRegisration union @lastRegisration');
        $this->assertEquals(
            $this->osuql->getSQL(['main']),
            '(select min(users.registration) as reg_interval from users) ' .
                'union ' .
                '(select max(users.registration) as reg_interval from users)'
        );
        $this->assertNull($this->osuql->getSQL('all'));
    }

    public function testCallbackModifier(): void
    {
        $this->osuql->addSelect('main');
        $this->osuql->getQuery('main')->addFrom('users');
        $this->osuql->getQuery('main')->addField('users', 'id');
        $this->osuql->getQuery('main')->getField('users', 'id')->addCallbackModifier(function ($ofield) {
            $ofield->getOSelect()->addWhere("{$ofield->getField()} > 5");
        });
        $this->assertEquals(
            $this->osuql->getSQL(['main']),
            'select users.id from users where users.id > 5'
        );
        $this->assertNull($this->osuql->getSQL('all'));
    }

    public function testFilterWhere(): void
    {
        $this->osuql->addSelect('main');
        $this->osuql->getQuery('main')->addFrom('users');
        $this->osuql->getQuery('main')->addField('users', ['id' => 'uid']);
        $this->osuql->getQuery('main')->addFilterWhere(':id', 'uid > :id');
        $this->assertEquals(
            $this->osuql->getSQL(['main']),
            'select users.id as uid from users'
        );

        $this->osuql->addSelect('main');
        $this->osuql->getQuery('main')->addFrom('users');
        $this->osuql->getQuery('main')->addField('users', ['id' => 'uid']);
        $this->osuql->getQuery('main')->addFilterWhere(':id', 'uid > :id');
        $this->osuql->params[':id'] = null;
        $this->assertEquals(
            $this->osuql->getSQL(['main']),
            'select users.id as uid from users'
        );

        $this->osuql->addSelect('main');
        $this->osuql->getQuery('main')->addFrom('users');
        $this->osuql->getQuery('main')->addField('users', ['id' => 'uid']);
        $this->osuql->getQuery('main')->addFilterWhere(':id', 'uid > :id');
        $this->osuql->setParam(':id', new SuQLSimpleParam($this->osuql->getQuery('main')->getField('users', ['id' => 'uid']), [5]));
        $this->assertEquals(
            $this->osuql->getSQL(['main']),
            'select users.id as uid from users where users.id > :id'
        );
    }

    public function testInsert(): void
    {
        $this->osuql->addInsert('main');
        $this->osuql->getQuery('main')->addInto('users');
        $this->osuql->getQuery('main')->addValue('id', 1);
        $this->osuql->getQuery('main')->addValue('name', 'Yuriy');
        $this->assertEquals(
            $this->osuql->getSQL(['main']),
            "insert into users (id,name) values (1,'Yuriy')"
        );
        $this->assertNull($this->osuql->getSQL('all'));
    }

    public function testInsertWithPlaceholder(): void
    {
        $this->osuql->addInsert('main');
        $this->osuql->getQuery('main')->addInto('users');
        $this->osuql->getQuery('main')->addPlaceholder('id', ':id');
        $this->osuql->getQuery('main')->addPlaceholder('name', ':name');
        $this->assertEquals(
            $this->osuql->getSQL(['main']),
            "insert into users (id,name) values (:id,:name)"
        );
        $this->assertNull($this->osuql->getSQL('all'));
    }
}
