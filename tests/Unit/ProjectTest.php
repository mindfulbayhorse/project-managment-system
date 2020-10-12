<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use \App\Project;
use \App\Status;
use Illuminate\Database\Eloquent\Collection;
use \App\WorkBreakdownStructure;
use \App\Resource;

class ProjectTest extends TestCase
{
    use WithFaker, RefreshDatabase;
    
    protected $project;
    private $projectWbs;
    private $wbsNew;
    /**
     * A basic test example.
     *
     * @return void
     */
    protected function setUp(): void
    {
        
        parent::setUp();
        
        //new project is created
        $this->project = factory(Project::class)->create([
                        'title' => 'Beadshine',
        ]);
        
        $this->projectWbs = factory(WorkBreakdownStructure::class)->create([
        	'project_id'=>$this->project->id]);
        
        $this->wbsNew = factory(WorkBreakdownStructure::class)->make();
        
        $this->project->actualizeWBS($this->projectWbs);
    }
    
    /** @test*/
    public function it_has_a_path()
    {
        $this->assertEquals('/projects/'.$this->project->id, 
                        $this->project->path());
    }
        
    /** @test */
    public function project_has_a_title()
    {
        $this->assertEquals('Beadshine', $this->project->title);
    }
       
    /** @test */
    public function it_can_have_no_one_status()
    {
        $this->assertEmpty($this->project->status);
    }
    
    /** @test */
    public function it_has_defined_limit_of_wbs()
    {
    	
    	$this->assertEquals(2, $this->project->wbsLimit);
    }
    
    /** @test */
    public function it_can_have_status_added()
    {
        $status = factory(Status::class)->create(['name' => 'Initiated']);
        
        $this->project->status_id = $status->id;
        $this->project->save();
        
        $this->assertEquals('Initiated', $this->project->status->name);
    }
    
    /** @test */
    public function it_can_have_initial_wbs()
    {

        $this->project->initializeWBS($this->wbsNew);

        $this->assertInstanceOf(Collection::class, $this->project->wbs);
        
        $this->project->refresh();
        
        $this->assertCount(2, $this->project->wbs);
    }
    
    /** @test */
    public function it_can_have_only_one_actual_wbs()
    {
    	$this->withoutExceptionHandling();
    	
        $this->assertCount(1, $this->project->wbs()->actual());
        
        $this->project->initializeWBS($this->wbsNew);
        
        $this->project->actualizeWBS($this->wbsNew);
        
        $this->project->refresh();
        
        $this->assertCount(1, $this->project->wbs()->actual());
    }
    
    /** @test */
    public function it_can_limit_new_created_wbs()
    {
    	$this->withoutExceptionHandling();
    	
    	$this->assertCount(1, $this->project->wbs);
    	
    	$this->project->initializeWBS($this->wbsNew);
    	
    	$this->project->refresh();
    	
    	$this->assertCount(2, $this->project->wbs);
    	
    	$wbsThird = factory(WorkBreakdownStructure::class)->make();
        
        $this->expectException('Exception');
        $this->project->initializeWBS($wbsThird);
        
        $this->project->refresh();
        $this->assertCount(2, $this->project->wbs);
    }
    
    /** @test */
    public function it_can_limit_several_new_created_wbs()
    {
    	
    	$wbs = factory(WorkBreakdownStructure::class, 2)->make();
    	
    	$this->expectException('Exception');
    	$this->project->initializeWBS($wbs);
    	$this->project->refresh();
    	
    	$this->assertCount(2, $this->project->wbs);
    }    
    
}
