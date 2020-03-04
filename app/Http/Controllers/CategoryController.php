<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $action = $input['action'];
            try {
                switch ($action) {
                    case 'get_categories':
                        $result = $this->getCategories();
                        break;
                    // Перемещаем категорию
                    case 'move_category':
                        $result = $this->moveCategory($input);
                        break;
                    // Новая категория
                    case 'create_category':
                        $result = $this->newCategory($input);
                        break;
                    // Переименовываем категорию
                    case 'rename_category':
                        $id = isset($input['id']) && $input['id'] !== '#' ? (int)$input['id'] : 0;
                        $text = isset($input['text']) && $input['text'] !== '' ? $input['text'] : '';
                        $model = Category::find($id);
                        $model->category = $text;
                        $model->update();
                        $result='OK';
                        break;
                    // Удаляем категорию
                    case 'delete_category':
                        $id = isset($input['id']) && $input['id'] !== '#' ? (int)$input['id'] : 0;
                        //удаляем всех потомков
                        $childs = Category::where(['parent_id'=>$id])->get();
                        foreach ($childs as $child){
                            $child->delete();
                        }
                        //удаляем родителя
                        $parent = Category::find($id);
                        if(!empty($parent))
                            $parent->delete();
                        $result='OK';
                        break;

                    default:
                        $result = 'unknown action';
                        break;
                }

                // Возвращаем клиенту успешный ответ
                return json_encode(array(
                    'code' => 'success',
                    'result' => $result
                ));
            } catch (Exception $e) {
                // Возвращаем клиенту ответ с ошибкой
                return json_encode(array(
                    'code' => 'error',
                    'message' => $e->getMessage()
                ));
            }
        }
        return 'ERR';
    }

    // Вытаскиваем категории из БД
    private function getCategories()
    {
        $rows = DB::select("SELECT id AS `id`, IF (parent_id = 0, '#', parent_id) AS `parent`, category as `text`
                                    FROM categories ORDER BY `parent`, `position`");
        $categories = array();
        if (!empty($rows)) {
            foreach ($rows as $row) {
                array_push($categories, array(
                    'id' => $row->id,
                    'parent' => $row->parent,
                    'text' => $row->text
                ));
            }
        }
        return $categories;
    }

    // Перемещение категории
    private function moveCategory($params) {
        $categoryId = (int)$params['id'];
        $oldParentId = (int)$params['old_parent'];
        $newParentId = (int)$params['new_parent'];
        $oldPosition = (int)$params['old_position'] + 1;
        $newPosition = (int)$params['new_position'] + 1;
        // Исключение категории по ее родителю и позиции
        DB::update("update categories set position = position - 1 where parent_id = $oldParentId and position > $oldPosition");
        // Вставка категории по ее id, родителю и позиции number
        DB::update("update categories set position = position + 1 where parent_id = $newParentId and position >= $newPosition");
        DB::update("update categories set parent_id = $newParentId, position = $newPosition where id = $categoryId");
        return json_encode(array('code' => 'success'));
    }

    // Новая категория
    private function newCategory($params) {
        $node = isset($params['id']) && $params['id'] !== '#' ? (int)$params['id'] : 0;
        $nodeText = isset($params['text']) && $params['text'] !== '' ? $params['text'] : '';
        $position = isset($params['position']) ? $params['position'] : 1;
        $model = new Category();
        $model->category = $nodeText;
        $model->parent_id = $node;
        $model->position = $position;
        if($model->save())
            return json_encode(array('id' => $model->id));
    }
}
