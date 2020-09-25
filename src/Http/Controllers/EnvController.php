<?php

namespace Dcat\Admin\Extension\Env\Http\Controllers;

// use Dcat\Admin\Controllers\HasResourceActions;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Grid;
use Dcat\Admin\Form;
use Dcat\Admin\Extension\Env\Http\Repository\Env;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EnvController extends Controller
{
    private $model;

    public function __construct(Request $request)
    {
        $this->model = new Env($request);
    }

    public function index(Content $content)
    {
        $grid = new Grid($this->model);

        $grid->key();
        $grid->value();

        $grid->disableEditButton();
        $grid->showQuickEditButton();
        $grid->disableViewButton();
        $grid->enableDialogCreate();
        $grid->tableCollapse(false);

        return $content
            ->title('Env')
            ->body($grid);
    }

    public function editor($id, Content $content)
    {
        $env = $this->model->findOrFail($id);

        $form = new Form($this->model);

        $form->text('key')->value($env['key'])->required()->rules('required|min:3|alpha|max:20');
        $form->textarea('value')->value($env['value'])->required();
        
        return "<style>.form-editor .row{ margin-right:0; margin-left:0; }</style><section class=\"form-editor\">{$form->render()}</section>";
    }

    public function create(Content $content)
    {
        $form = new Form($this->model);

        $form->text('key')->required()->rules('required|min:3|alpha|max:20');
        $form->textarea('value')->required();
        
        $form->disableFooter();

        return "<style>.form-editor .row{ margin-right:0; margin-left:0; }</style><section class=\"form-editor\">{$form->render()}</section>";
    }

    public function toCreate(Request $request)
    {
        $this->model->key = $request->input('key');
        $this->model->value = $request->input('value');

        $this->model->save();

        $form = new Form();

        return $form->success('配置信息保存成功');
    }

    public function toEditor($id, Request $request)
    {
        $form = new Form();

        $this->model->key = $request->input('key');
        $this->model->value = $request->input('value');

        $this->model->save();

        return $form->success('配置信息保存成功');
    }

    public function toDelete($id, Request $request)
    {
        $form = new Form();

        $this->model->key = $request->input('key');

        $affected = $this->model->delete();

        if ($affected <= 0) {
            return $form->error('配置信息删除失败');
        }

        return $form->success('配置信息删除成功');
    }
}
