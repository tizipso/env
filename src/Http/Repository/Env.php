<?php
namespace Dcat\Admin\Extension\Env\Http\Repository;

use Dcat\Admin\Grid\Model;
use Dcat\Admin\Extension\Env\Http\Models\Env as EnvModel;
use Dcat\Admin\Repositories\EloquentRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class Env extends EloquentRepository
{
    protected $eloquentClass = EnvModel::class;

    private $env;

    public function __construct(Request $request, $repository = null)
    {
        $this->env = config('admin.extensions.env.path', base_path().'/.env');

        parent::__construct($request);
    }

    public function paginate()
    {
        $perPage = request()->get('per_page', 20);
        $page = request()->get('page', 1);
        $start = ($page - 1) * $perPage;
        $data = $this->getEnv();
        $list = array_slice($data, $start, $perPage);
        
        $paginator = new LengthAwarePaginator($list, count($data), $perPage, $page);
        $paginator->setPath(url()->current());
        
        return $paginator;
    }

    public function get(Model $model)
    {
        return $this->paginate();
    }

    public function findOrFail($id)
    {
        return $this->getEnv($id);
    }


    public function save(array $options = [])
    {
        return $this->setEnv($this->key, $this->value);
    }

    public function delete(array $options = [])
    {
        return $this->deleteEnv($this->key);
    }

    /**
     * @param $id
     * @return bool|null|void
     */
    public function deleteEnv($id)
    {
        $ids = explode(',', $id);
        $data = $this->getEnv();
        foreach ($ids as $val) {
            $index = array_search($val, array_column($data, 'id'));
            unset($data[$index]);
        }
        return $this->saveEnv($data);
    }

    /**
     * Get .env variable.
     * @param null $id
     * @return array|mixed
     */
    private function getEnv($id = null)
    {
        $string = file_get_contents($this->env);
        $string = preg_split('/\n+/', $string);
        $array = [];
        foreach ($string as $k => $one) {
            if (preg_match('/^(#\s)/', $one) === 1 || preg_match('/^([\\n\\r]+)/', $one)) {
                continue;
            }
            $entry = explode("=", $one, 2);
            if (!empty($entry[0])) {
                $array[] = ['id' => $k + 1, 'key' => $entry[0], 'value' => isset($entry[1]) ? $entry[1] : null];
            }
        }
        if (empty($id)) {
            return $array;
        }
        $index = array_search($id, array_column($array, 'id'));

        return $array[$index];
    }

    /**
     * Update or create .env variable.
     * @param $key
     * @param $value
     * @return bool
     */
    private function setEnv($key, $value)
    {
        $array = $this->getEnv();
        $index = array_search($key, array_column($array, 'key'));
        if ($index !== false) {
            $array[$index]['value'] = $value; // 更新
        } else {
            array_push($array, ['key' => $key, 'value' => $value]); // 新增
        }
        return $this->saveEnv($array);
    }

    /**
     * Save .env variable.
     * @param $array
     * @return bool
     */
    private function saveEnv($array)
    {
        if (is_array($array)) {
            $newArray = [];
            $i = 0;
            foreach ($array as $env) {
                if (preg_match('/\s/', $env['value']) > 0 && (strpos($env['value'], '"') > 0 && strpos($env['value'], '"', -0) > 0)) {
                    $env['value'] = '"'.$env['value'].'"';
                }
                $newArray[$i] = $env['key']."=".$env['value'];
                $i++;
            }
            $newArray = implode("\n", $newArray);
            file_put_contents($this->env, $newArray);
            return true;
        }
        return false;
    }
}
