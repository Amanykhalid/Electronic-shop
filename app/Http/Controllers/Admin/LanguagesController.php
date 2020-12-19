<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LanguageRequest;

use App\Models\languages as ModelsLanguages;

class LanguagesController extends Controller
{
    //
    public function index(){
        $Languages=ModelsLanguages::select()->paginate(PAGINATION_COUNT);
        return view('admin/languages/index',compact('Languages'));
    }

    public function createLanguages(Request $req)
    {
        return view('admin/languages/create');
    }

    public function storeLanguages(LanguageRequest $req)
    {
        try {
            ModelsLanguages::create($req->except(['_token']));
            return redirect()->route('admin.Languages')->with(['success' => 'تم حفظ اللغة بنجاح']);
        } catch (\Exception $ex) {
            return redirect()->route('admin.Languages')->with(['error' => 'هناك خطا ما يرجي المحاوله فيما بعد']);
        }
    }

    
    public function editLanguages($id)
    {
        $language = ModelsLanguages::select()->find($id);
        if (!$language) {
            return redirect()->route('admin.Languages')->with(['error' => 'هذه اللغة غير موجوده']);
        }

        return view('admin.Languages.edit', compact('language'));
    }

    public function updateLanguages($id,LanguageRequest $request)
    {
        try {
            $language = ModelsLanguages::find($id);
            if (!$language) {
                return redirect()->route('admin.Languages.edit', $id)->with(['error' => 'هذه اللغة غير موجوده']);
            } 


            if (!$request->has('active'))
                $request->request->add(['active' => 0]);
            $language->update($request->except('_token'));
            return redirect()->route('admin.Languages')->with(['success' => 'تم تحديث اللغة بنجاح']);

        } catch (\Exception $ex) {
            return redirect()->route('admin.Languages')->with(['error' => 'هناك خطا ما يرجي المحاوله فيما بعد']);
        }
    }

    public function deleteLanguages($id)
    {
        try {
            $language = ModelsLanguages::find($id);
            if (!$language) {
                return redirect()->route('admin.Languages', $id)->with(['error' => 'هذه اللغة غير موجوده']);
            }
            $language->delete();
            return redirect()->route('admin.Languages')->with(['success' => 'تم حذف اللغة بنجاح']);

        } catch (\Exception $ex) {
            return redirect()->route('admin.Languages')->with(['error' => 'هناك خطا ما يرجي المحاوله فيما بعد']);
        }
    }
}
