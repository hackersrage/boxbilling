<?php
namespace Box\Tests\Mod\Kb\Api;

class AdminTest extends \PHPUnit_Framework_TestCase
{
    public function testArticle_get_list()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $data = array(
            'status' => 'status',
            'search' => 'search',
            'cat'    => 'category'
        );

        $kbService = $this->getMockBuilder('Box\Mod\Kb\Service')->setMethods(array('searchArticles'))->getMock();
        $kbService->expects($this->atLeastOnce())
            ->method('searchArticles')
            ->will($this->returnValue(array('list' => array())));

        $adminApi->setService($kbService);

        $result = $adminApi->article_get_list($data);
        $this->assertInternalType('array', $result);

    }

    public function testArticle_get()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $data = array(
            'id' => rand(1, 100),
        );

        $db = $this->getMockBuilder('Box_Database')->getMock();
        $db->expects($this->atLeastOnce())
            ->method('findOne')
            ->will($this->returnValue(new \Model_KbArticle()));

        $admin     = new \Model_Admin();
        $admin->loadBean(new \RedBeanPHP\OODBBean());

        $admin->id = 5;

        $di                   = new \Box_Di();
        $di['loggedin_admin'] = $admin;
        $di['db']             = $db;
        $adminApi->setDi($di);

        $kbService = $this->getMockBuilder('Box\Mod\Kb\Service')->setMethods(array('toApiArray'))->getMock();
        $kbService->expects($this->atLeastOnce())
            ->method('toApiArray')
            ->will($this->returnValue(array()));
        $adminApi->setService($kbService);

        $result = $adminApi->article_get($data);
        $this->assertInternalType('array', $result);
    }

    /**
     * @expectedException \Box_Exception
     */
    public function testArticle_getIdNotSetException()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();
        $adminApi->article_get(array());
    }

    /**
     * @expectedException \Box_Exception
     */
    public function testArticle_getNotFoundException()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $data = array(
            'id' => rand(1, 100),
        );

        $db = $this->getMockBuilder('Box_Database')->getMock();
        $db->expects($this->atLeastOnce())
            ->method('findOne')
            ->will($this->returnValue(false));

        $di       = new \Box_Di();
        $di['db'] = $db;
        $adminApi->setDi($di);
        $adminApi->article_get($data);
    }

    public function testArticle_create()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $data = array(
            'kb_article_category_id' => rand(1, 100),
            'title'                  => 'Title',
        );

        $id = rand(1, 100);

        $kbService = $this->getMockBuilder('Box\Mod\Kb\Service')->setMethods(array('createArticle'))->getMock();
        $kbService->expects($this->atLeastOnce())
            ->method('createArticle')
            ->will($this->returnValue($id));
        $adminApi->setService($kbService);

        $result = $adminApi->article_create($data);
        $this->assertInternalType('integer', $result);
        $this->assertEquals($result, $id);
    }

    /**
     * @expectedException \Box_Exception
     */
    public function testArticle_createCategoryNotSetException()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $data = array(
            'title' => 'Title',
        );
        $adminApi->article_create($data);
    }

    /**
     * @expectedException \Box_Exception
     */
    public function testArticle_createTitleNotSetException()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $data = array(
            'kb_article_category_id' => rand(1, 100),
        );
        $adminApi->article_create($data);
    }

    public function testArticle_update()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $data = array(
            "id"                     => rand(1, 100),
            "kb_article_category_id" => rand(1, 100),
            "title"                  => "Title",
            "slug"                   => "article-slug",
            "status"                 => "active",
            "content"                => "Content",
            "views"                  => rand(1, 100),
        );

        $kbService = $this->getMockBuilder('Box\Mod\Kb\Service')->setMethods(array('updateArticle'))->getMock();
        $kbService->expects($this->atLeastOnce())
            ->method('updateArticle')
            ->will($this->returnValue(true));
        $adminApi->setService($kbService);

        $result = $adminApi->article_update($data);
        $this->assertTrue($result);
    }

    /**
     * @expectedException \Box_Exception
     */
    public function testArticle_updateIdNotSetException()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $kbService = $this->getMockBuilder('Box\Mod\Kb\Service')->setMethods(array('updateArticle'))->getMock();
        $kbService->expects($this->never())
            ->method('updateArticle')
            ->will($this->returnValue(true));
        $adminApi->setService($kbService);

        $result = $adminApi->article_update(array());
        $this->assertTrue($result);
    }

    public function testArticle_delete()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $data = array(
            "id" => rand(1, 100),
        );

        $db = $this->getMockBuilder('Box_Database')->getMock();
        $db->expects($this->atLeastOnce())
            ->method('findOne')
            ->will($this->returnValue(new \Model_KbArticle()));

        $di       = new \Box_Di();
        $di['db'] = $db;
        $adminApi->setDi($di);

        $kbService = $this->getMockBuilder('Box\Mod\Kb\Service')->setMethods(array('rm'))->getMock();
        $kbService->expects($this->atLeastOnce())
            ->method('rm')
            ->will($this->returnValue(true));
        $adminApi->setService($kbService);

        $result = $adminApi->article_delete($data);
        $this->assertTrue($result);
    }

    /**
     * @expectedException \Box_Exception
     */
    public function testArticle_deleteIdNotSetException()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $db = $this->getMockBuilder('Box_Database')->getMock();
        $db->expects($this->never())
            ->method('findOne')
            ->will($this->returnValue(new \Model_KbArticle()));

        $di       = new \Box_Di();
        $di['db'] = $db;
        $adminApi->setDi($di);

        $kbService = $this->getMockBuilder('Box\Mod\Kb\Service')->setMethods(array('rm'))->getMock();
        $kbService->expects($this->never())
            ->method('rm')
            ->will($this->returnValue(true));
        $adminApi->setService($kbService);

        $result = $adminApi->article_delete(array());
        $this->assertTrue($result);
    }

    /**
     * @expectedException \Box_Exception
     */
    public function testArticle_deleteNotFoundException()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $db = $this->getMockBuilder('Box_Database')->getMock();
        $db->expects($this->atLeastOnce())
            ->method('findOne')
            ->will($this->returnValue(false));

        $di       = new \Box_Di();
        $di['db'] = $db;
        $adminApi->setDi($di);

        $kbService = $this->getMockBuilder('Box\Mod\Kb\Service')->setMethods(array('rm'))->getMock();
        $kbService->expects($this->never())
            ->method('rm')
            ->will($this->returnValue(true));
        $adminApi->setService($kbService);

        $result = $adminApi->article_delete(array('id' => rand(1, 100)));
        $this->assertTrue($result);
    }

    public function testCategory_get_list()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $willReturn = array(
            "pages"    => 5,
            "page"     => 2,
            "per_page" => 2,
            "total"    => 10,
            "list"     => array(),
        );

        $pager = $this->getMockBuilder('Box_Pagination')->getMock();
        $pager->expects($this->atLeastOnce())
            ->method('getAdvancedResultSet')
            ->will($this->returnValue($willReturn));

        $di          = new \Box_Di();
        $di['pager'] = $pager;
        $adminApi->setDi($di);

        $kbService = $this->getMockBuilder('Box\Mod\Kb\Service')->setMethods(array('categoryGetSearchQuery'))->getMock();
        $kbService->expects($this->atLeastOnce())
            ->method('categoryGetSearchQuery')
            ->will($this->returnValue(true));
        $adminApi->setService($kbService);

        $result = $adminApi->category_get_list(array());
        $this->assertInternalType('array', $result);
        $this->assertEquals($result, $willReturn);
    }

    public function testCategory_get()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $db = $this->getMockBuilder('Box_Database')->getMock();
        $db->expects($this->atLeastOnce())
            ->method('findOne')
            ->will($this->returnValue(new \Model_KbArticleCategory()));

        $di       = new \Box_Di();
        $di['db'] = $db;
        $adminApi->setDi($di);

        $kbService = $this->getMockBuilder('Box\Mod\Kb\Service')->setMethods(array('categoryToApiArray'))->getMock();
        $kbService->expects($this->atLeastOnce())
            ->method('categoryToApiArray')
            ->will($this->returnValue(array()));
        $adminApi->setService($kbService);

        $data   = array(
            'id' => rand(1, 100)
        );
        $result = $adminApi->category_get($data);
        $this->assertInternalType('array', $result);
    }

    /**
     * @expectedException \Box_Exception
     */
    public function testCategory_getIdNotSetException()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $db = $this->getMockBuilder('Box_Database')->getMock();
        $db->expects($this->never())
            ->method('findOne')
            ->will($this->returnValue(new \Model_KbArticleCategory()));

        $di       = new \Box_Di();
        $di['db'] = $db;
        $adminApi->setDi($di);

        $kbService = $this->getMockBuilder('Box\Mod\Kb\Service')->setMethods(array('categoryToApiArray'))->getMock();
        $kbService->expects($this->never())
            ->method('categoryToApiArray')
            ->will($this->returnValue(array()));
        $adminApi->setService($kbService);

        $result = $adminApi->category_get(array());
        $this->assertInternalType('array', $result);
    }

    /**
     * @expectedException \Box_Exception
     */
    public function testCategory_getNotFoundException()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $db = $this->getMockBuilder('Box_Database')->getMock();
        $db->expects($this->atLeastOnce())
            ->method('findOne')
            ->will($this->returnValue(false));

        $di       = new \Box_Di();
        $di['db'] = $db;
        $adminApi->setDi($di);

        $kbService = $this->getMockBuilder('Box\Mod\Kb\Service')->setMethods(array('categoryToApiArray'))->getMock();
        $kbService->expects($this->never())
            ->method('categoryToApiArray')
            ->will($this->returnValue(array()));
        $adminApi->setService($kbService);

        $data = array(
            'id' => rand(1, 100)
        );

        $result = $adminApi->category_get($data);
        $this->assertInternalType('array', $result);
    }

    public function testCategory_create()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $kbService = $this->getMockBuilder('Box\Mod\Kb\Service')->setMethods(array('createCategory'))->getMock();
        $kbService->expects($this->atLeastOnce())
            ->method('createCategory')
            ->will($this->returnValue(array()));
        $adminApi->setService($kbService);

        $data = array(
            'title'       => 'Title',
            'description' => 'Description',
        );

        $result = $adminApi->category_create($data);
        $this->assertInternalType('array', $result);
    }

    /**
     * @expectedException \Box_Exception
     */
    public function testCategory_createTitleNotSetException()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $kbService = $this->getMockBuilder('Box\Mod\Kb\Service')->setMethods(array('createCategory'))->getMock();
        $kbService->expects($this->never())
            ->method('createCategory')
            ->will($this->returnValue(array()));
        $adminApi->setService($kbService);

        $data = array();

        $result = $adminApi->category_create($data);
        $this->assertInternalType('array', $result);
    }

    public function testCategory_update()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $kbService = $this->getMockBuilder('Box\Mod\Kb\Service')->setMethods(array('updateCategory'))->getMock();
        $kbService->expects($this->atLeastOnce())
            ->method('updateCategory')
            ->will($this->returnValue(array()));
        $adminApi->setService($kbService);


        $db = $this->getMockBuilder('Box_Database')->getMock();
        $db->expects($this->atLeastOnce())
            ->method('findOne')
            ->will($this->returnValue(new \Model_KbArticleCategory()));

        $di       = new \Box_Di();
        $di['db'] = $db;
        $adminApi->setDi($di);


        $data = array(
            'id'          => rand(1, 100),
            'title'       => 'Title',
            'slug'        => 'category-slug',
            'description' => 'Description',
        );

        $result = $adminApi->category_update($data);
        $this->assertInternalType('array', $result);
    }

    /**
     * @expectedException \Box_Exception
     */
    public function testCategory_updateIdNotSet()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $kbService = $this->getMockBuilder('Box\Mod\Kb\Service')->setMethods(array('updateCategory'))->getMock();
        $kbService->expects($this->never())
            ->method('updateCategory')
            ->will($this->returnValue(array()));
        $adminApi->setService($kbService);


        $db = $this->getMockBuilder('Box_Database')->getMock();
        $db->expects($this->never())
            ->method('findOne')
            ->will($this->returnValue(new \Model_KbArticleCategory()));

        $di       = new \Box_Di();
        $di['db'] = $db;
        $adminApi->setDi($di);


        $data = array();

        $result = $adminApi->category_update($data);
        $this->assertInternalType('array', $result);
    }

    /**
     * @expectedException \Box_Exception
     */
    public function testCategory_updateNotFound()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $kbService = $this->getMockBuilder('Box\Mod\Kb\Service')->setMethods(array('updateCategory'))->getMock();
        $kbService->expects($this->never())
            ->method('updateCategory')
            ->will($this->returnValue(array()));
        $adminApi->setService($kbService);


        $db = $this->getMockBuilder('Box_Database')->getMock();
        $db->expects($this->atLeastOnce())
            ->method('findOne')
            ->will($this->returnValue(false));

        $di       = new \Box_Di();
        $di['db'] = $db;
        $adminApi->setDi($di);


        $data = array(
            'id'          => rand(1, 100),
            'title'       => 'Title',
            'slug'        => 'category-slug',
            'description' => 'Description',
        );

        $result = $adminApi->category_update($data);
        $this->assertInternalType('array', $result);
    }

    public function testCategory_delete()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $kbService = $this->getMockBuilder('Box\Mod\Kb\Service')->setMethods(array('categoryRm'))->getMock();
        $kbService->expects($this->atLeastOnce())
            ->method('categoryRm')
            ->will($this->returnValue(array()));
        $adminApi->setService($kbService);


        $db = $this->getMockBuilder('Box_Database')->getMock();
        $db->expects($this->atLeastOnce())
            ->method('findOne')
            ->will($this->returnValue(new \Model_KbArticleCategory()));

        $di       = new \Box_Di();
        $di['db'] = $db;
        $adminApi->setDi($di);

        $data   = array(
            'id' => rand(1, 100),
        );
        $result = $adminApi->category_delete($data);
        $this->assertInternalType('array', $result);
    }

    /**
     * @expectedException \Box_Exception
     */
    public function testCategory_deleteIdNotSet()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $kbService = $this->getMockBuilder('Box\Mod\Kb\Service')->setMethods(array('categoryRm'))->getMock();
        $kbService->expects($this->never())
            ->method('categoryRm')
            ->will($this->returnValue(array()));
        $adminApi->setService($kbService);


        $db = $this->getMockBuilder('Box_Database')->getMock();
        $db->expects($this->never())
            ->method('findOne')
            ->will($this->returnValue(new \Model_KbArticleCategory()));

        $di       = new \Box_Di();
        $di['db'] = $db;
        $adminApi->setDi($di);

        $data   = array();
        $result = $adminApi->category_delete($data);
        $this->assertInternalType('array', $result);
    }


    /**
     * @expectedException \Box_Exception
     */
    public function testCategory_deleteNotFound()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $kbService = $this->getMockBuilder('Box\Mod\Kb\Service')->setMethods(array('categoryRm'))->getMock();
        $kbService->expects($this->never())
            ->method('categoryRm')
            ->will($this->returnValue(array()));
        $adminApi->setService($kbService);


        $db = $this->getMockBuilder('Box_Database')->getMock();
        $db->expects($this->atLeastOnce())
            ->method('findOne')
            ->will($this->returnValue(false));

        $di       = new \Box_Di();
        $di['db'] = $db;
        $adminApi->setDi($di);

        $data   = array(
            'id' => rand(1, 100)
        );
        $result = $adminApi->category_delete($data);
        $this->assertInternalType('array', $result);
    }

    public function testCategory_get_pairs()
    {
        $adminApi = new \Box\Mod\Kb\Api\Admin();

        $kbService = $this->getMockBuilder('Box\Mod\Kb\Service')->setMethods(array('categoryGetPairs'))->getMock();
        $kbService->expects($this->atLeastOnce())
            ->method('categoryGetPairs')
            ->will($this->returnValue(array()));
        $adminApi->setService($kbService);

        $result = $adminApi->category_get_pairs(array());
        $this->assertInternalType('array', $result);
    }


}
