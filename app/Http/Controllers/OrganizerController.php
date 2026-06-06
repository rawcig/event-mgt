<?php

namespace App\Http\Controllers;

use App\Models\Organizer;
use App\Http\Requests\CreateOrganizerRequest;
use App\Http\Requests\UpdateOrganizerRequest;
use Illuminate\Http\Request;

class OrganizerController extends Controller
{
    /**
     * list all organizers
     */
    public function index()
    {
        $organizers = Organizer::latest()->paginate(10);
        return view('backend.pages.organizer.index', compact('organizers'));
    }

    /**
     * show create form
     */
    public function create()
    {
        $this->authorize('create', Organizer::class);
        return view('backend.pages.organizer.create');
    }

    /**
     * store new organizer
     */
    public function store(CreateOrganizerRequest $request)
    {
        $this->authorize('create', Organizer::class);
        $validatedData = $request->validated();
        
        // handle logo upload
        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $imageName = uniqid('logo_') . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('storage/organizers'), $imageName);
            $validatedData['logo'] = 'organizers/' . $imageName;
        }
        
        Organizer::create($validatedData);
        return redirect()->route('organizer.index')->with('success', 'organizer created!');
    }

    /**
     * show organizer details
     */
    public function show(string $id)
    {
        $organizer = Organizer::with('events')->findOrFail($id);
        return view('backend.pages.organizer.show', compact('organizer'));
    }

    /**
     * show edit form
     */
    public function edit(string $id)
    {
        $organizer = Organizer::findOrFail($id);
        $this->authorize('update', $organizer);
        return view('backend.pages.organizer.edit', compact('organizer'));
    }

    /**
     * update organizer
     */
    public function update(UpdateOrganizerRequest $request, string $id)
    {
        $organizer = Organizer::findOrFail($id);
        $this->authorize('update', $organizer);
        $validatedData = $request->validated();
        
        // handle logo upload
        if ($request->hasFile('logo')) {
            // delete old logo
            if ($organizer->logo) {
                $oldLogoPath = public_path('storage/' . $organizer->logo);
                if (file_exists($oldLogoPath)) {
                    unlink($oldLogoPath);
                }
            }
            
            // upload new logo
            $image = $request->file('logo');
            $imageName = uniqid('logo_') . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('storage/organizers'), $imageName);
            $validatedData['logo'] = 'organizers/' . $imageName;
        }
        
        $organizer->update($validatedData);
        return redirect()->route('organizer.index')->with('success', 'organizer updated!');
    }

    /**
     * delete organizer
     */
    public function destroy(string $id)
    {
        $organizer = Organizer::findOrFail($id);
        $this->authorize('delete', $organizer);
        $organizer->delete();
        return redirect()->route('organizer.index')->with('success', 'organizer deleted!');
    }
}
