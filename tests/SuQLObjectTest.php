<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use suql\core\SuQLObject;
use suql\core\SuQLScheme;
use suql\builder\SQLDriver;
use suql\core\SuQLPlaceholder;
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

    public function testSimpleJoin(): void
    {
        $sql =
            'select ' .
                'users.id, '.
                'groups.id as gid, ' .
                'groups.name as gname ' .
            'from users ' .
            'inner join user_group on users.id = user_group.user_id ' .
            'inner join groups on user_group.group_id = groups.id';

        $this->osuql->addSelect('simple_join');
        $this->osuql->getQuery('simple_join')->addFrom('users');
        $this->osuql->getQuery('simple_join')->addField('users', 'id');
        $this->osuql->getQuery('simple_join')->addJoin('inner', 'user_group');
        $this->osuql->getQuery('simple_join')->addJoin('inner', 'groups');
        $this->osuql->getQuery('simple_join')->addField('groups', 'id@gid');
        $this->osuql->getQuery('simple_join')->addField('groups', 'name@gname');
        $suql = $this->osuql->getSQL(['simple_join']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['simple_join']));
    }

    public function testJoinWithWhere(): void
    {
        $sql =
            'select ' .
                'users.id, ' .
                'users.registration, ' .
                'groups.name as group ' .
            'from users ' .
            'inner join user_group on users.id = user_group.user_id ' .
            'inner join groups on user_group.group_id = groups.id ' .
            "where groups.name = 'admin'";

        $this->osuql->addSelect('join_with_where');
        $this->osuql->getQuery('join_with_where')->addFrom('users');
        $this->osuql->getQuery('join_with_where')->addField('users', 'id');
        $this->osuql->getQuery('join_with_where')->addField('users', 'registration');
        $this->osuql->getQuery('join_with_where')->addJoin('inner', 'user_group');
        $this->osuql->getQuery('join_with_where')->addJoin('inner', 'groups');
        $this->osuql->getQuery('join_with_where')->addField('groups', ['name' => 'group']);
        $this->osuql->getQuery('join_with_where')->addWhere("group = 'admin'");
        $suql = $this->osuql->getSQL(['join_with_where']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['join_with_where']));
    }

    public function testJoinWithSubQuery(): void
    {
        $sql =
            'select ' .
                'users.id ' .
            'from users ' .
            'inner join (' .
                'select ' .
                    'users.id ' .
                'from users' .
            ') sub_query on users.id = sub_query.id';

        $this->osuql->getScheme()->rel(['users' => 'u'], ['sub_query' => 'v'], 'u.id = v.id');

        $this->osuql->addSelect('main_query');
        $this->osuql->getQuery('main_query')->addFrom('users');
        $this->osuql->getQuery('main_query')->addField('users', 'id');
        $this->osuql->getQuery('main_query')->addJoin('inner', 'sub_query');

        $this->osuql->addSelect('sub_query');
        $this->osuql->getQuery('sub_query')->addFrom('users');
        $this->osuql->getQuery('sub_query')->addField('users', 'id');

        $suql = $this->osuql->getSQL(['main_query']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['main_query']));
    }

    public function testSelectGroup(): void
    {
        $sql =
            'select ' .
                'groups.name as gname, ' .
                'count(groups.name) as count ' .
            'from users ' .
            'inner join user_group on users.id = user_group.user_id ' .
            'inner join groups on user_group.group_id = groups.id ' .
            "where groups.name = 'admin' " .
            'group by groups.name';

        $this->osuql->addSelect('select_group');
        $this->osuql->getQuery('select_group')->addFrom('users');
        $this->osuql->getQuery('select_group')->addJoin('inner', 'user_group');
        $this->osuql->getQuery('select_group')->addJoin('inner', 'groups');
        $this->osuql->getQuery('select_group')->addField('groups', 'name@gname');
        $this->osuql->getQuery('select_group')->addField('groups', 'name@count');
        $this->osuql->getQuery('select_group')->getField('groups', 'name@count')->addModifier('group');
        $this->osuql->getQuery('select_group')->getField('groups', 'name@count')->addModifier('count');
        $this->osuql->getQuery('select_group')->addWhere("gname = 'admin'");
        $suql = $this->osuql->getSQL(['select_group']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_group']));
    }

    public function testSubQueries(): void
    {
        $sql =
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
            "where gname = 'admin'";

        $this->osuql->addSelect('main_query');
        $this->osuql->getQuery('main_query')->addFrom('allGroupCount');
        $this->osuql->getQuery('main_query')->addField('allGroupCount', 'gname');
        $this->osuql->getQuery('main_query')->addField('allGroupCount', 'count');
        $this->osuql->getQuery('main_query')->addWhere("gname = 'admin'");

        $this->osuql->addSelect('allGroupCount');
        $this->osuql->getQuery('allGroupCount')->addFrom('users');
        $this->osuql->getQuery('allGroupCount')->addJoin('inner', 'user_group');
        $this->osuql->getQuery('allGroupCount')->addJoin('inner', 'groups');
        $this->osuql->getQuery('allGroupCount')->addField('groups', 'name@gname');
        $this->osuql->getQuery('allGroupCount')->addField('groups', 'name@count');
        $this->osuql->getQuery('allGroupCount')->getField('groups', 'name@count')->addModifier('group');
        $this->osuql->getQuery('allGroupCount')->getField('groups', 'name@count')->addModifier('count');
        
        $suql = $this->osuql->getSQL(['main_query']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['main_query']));
    }

    public function testSelectOrder(): void
    {
        $sql =
            'select ' .
                'groups.name as gname, ' .
                'count(groups.name) as count ' .
            'from users ' .
            'inner join user_group on users.id = user_group.user_id ' .
            'inner join groups on user_group.group_id = groups.id ' .
            'group by groups.name ' .
            'order by count asc';

        $this->osuql->addSelect('select_order');
        $this->osuql->getQuery('select_order')->addFrom('users');
        $this->osuql->getQuery('select_order')->addJoin('inner', 'user_group');
        $this->osuql->getQuery('select_order')->addJoin('inner', 'groups');
        $this->osuql->getQuery('select_order')->addField('groups', 'name@gname');
        $this->osuql->getQuery('select_order')->addField('groups', 'name@count');
        $this->osuql->getQuery('select_order')->getField('groups', 'name@count')->addModifier('group');
        $this->osuql->getQuery('select_order')->getField('groups', 'name@count')->addModifier('count');
        $this->osuql->getQuery('select_order')->getField('groups', 'name@count')->addModifier('asc');
        $suql = $this->osuql->getSQL(['select_order']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['select_order']));
    }

    public function testUnion(): void
    {
        $sql =
            '(select min(users.registration) as reg_interval from users) ' .
                'union ' .
            '(select max(users.registration) as reg_interval from users)';

        $this->osuql->addSelect('firstRegisration');
        $this->osuql->getQuery('firstRegisration')->addFrom('users');
        $this->osuql->getQuery('firstRegisration')->addField('users', 'registration@reg_interval');
        $this->osuql->getQuery('firstRegisration')->getField('users', 'registration@reg_interval')->addModifier('min');

        $this->osuql->addSelect('lastRegisration');
        $this->osuql->getQuery('lastRegisration')->addFrom('users');
        $this->osuql->getQuery('lastRegisration')->addField('users', 'registration@reg_interval');
        $this->osuql->getQuery('lastRegisration')->getField('users', 'registration@reg_interval')->addModifier('max');

        $this->osuql->addUnion('main_query', '@firstRegisration union @lastRegisration');
        $suql = $this->osuql->getSQL(['main_query']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['main_query']));
    }

    public function testCallbackModifier(): void
    {
        $sql =
            'select '.
                'users.id '.
            'from users '.
            'where users.id > 3';

        $this->osuql->addSelect('callback_modifier');
        $this->osuql->getQuery('callback_modifier')->addFrom('users');
        $this->osuql->getQuery('callback_modifier')->addField('users', 'id');
        $this->osuql->getQuery('callback_modifier')->getField('users', 'id')->addCallbackModifier(function ($ofield) {
            $ofield->getOSelect()->addWhere("{$ofield->getField()} > 3");
        });
        $suql = $this->osuql->getSQL(['callback_modifier']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['callback_modifier']));
    }

    public function testFilterEmpty(): void
    {
        $sql =
            'select '.
                'users.id as uid '.
            'from users';

        $this->osuql->addSelect('empty_filter');
        $this->osuql->getQuery('empty_filter')->addFrom('users');
        $this->osuql->getQuery('empty_filter')->addField('users', ['id' => 'uid']);
        $this->osuql->getQuery('empty_filter')->addFilterWhere(':id', 'uid > :id');
        $this->osuql->setParam(':id', null);
        $suql = $this->osuql->getSQL(['empty_filter']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['empty_filter']));
    }

    public function testFilterNotEmpty(): void
    {
        $sql =
            'select '.
                'users.id as uid '.
            'from users '.
            "where users.id > {{placeholder}}";

        $this->osuql->addSelect('not_empty_filter');
        $this->osuql->getQuery('not_empty_filter')->addFrom('users');
        $this->osuql->getQuery('not_empty_filter')->addField('users', ['id' => 'uid']);

        $param = new SuQLSimpleParam($this->osuql->getQuery('not_empty_filter')->getField('users', ['id' => 'uid']), [5]);
        $placeholder = $param->getPlaceholder();

        $this->osuql->getQuery('not_empty_filter')->addFilterWhere($placeholder, "uid > $placeholder");
        $this->osuql->setParam($placeholder, $param);
        $suql = $this->osuql->getSQL(['not_empty_filter']);
        $sql = str_replace('{{placeholder}}', $placeholder, $sql);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['not_empty_filter']));

        $this->assertEquals($this->osuql->getParamList(), [
            ':ph0_fc02896e3034a4ed53259916e2e2d82d' => 5,
        ]);
    }

    public function testInsert(): void
    {
        $sql = "insert into users (id,name) values (1,'Yuriy')";

        $this->osuql->addInsert('main');
        $this->osuql->getQuery('main')->addInto('users');
        $this->osuql->getQuery('main')->addValue('id', 1);
        $this->osuql->getQuery('main')->addValue('name', 'Yuriy');
        $suql = $this->osuql->getSQL(['main']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['main']));
    }

    public function testInsertWithPlaceholder(): void
    {
        $sql = 'insert into users (id,name) values (:id,:name)';

        $this->osuql->addInsert('main');
        $this->osuql->getQuery('main')->addInto('users');
        $this->osuql->getQuery('main')->addPlaceholder('id', ':id');
        $this->osuql->getQuery('main')->addPlaceholder('name', ':name');
        $suql = $this->osuql->getSQL(['main']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['main']));
    }
}
