<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MediaFile;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Student;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $query = MediaFile::with(['course', 'batch', 'student', 'uploader'])->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('file_type')) {
            $query->where('file_type', $request->file_type);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('title', 'like', "%{$s}%");
        }

        $mediaFiles = $query->paginate(16)->withQueryString();

        $courses = Course::where('status', 'Active')->get();
        $batches = Batch::whereIn('status', ['Upcoming', 'Ongoing'])->get();
        $students = Student::where('status', 'Active')->get();

        $categories = [
            'All' => MediaFile::count(),
            'Student Photos' => MediaFile::where('category', 'Student Photos')->count(),
            'Student Videos' => MediaFile::where('category', 'Student Videos')->count(),
            'Classroom Activities' => MediaFile::where('category', 'Classroom Activities')->count(),
            'Events' => MediaFile::where('category', 'Events')->count(),
            'Workshops' => MediaFile::where('category', 'Workshops')->count(),
            'Course Materials' => MediaFile::where('category', 'Course Materials')->count(),
        ];

        return view('media.index', compact('mediaFiles', 'courses', 'batches', 'students', 'categories'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'course_id' => 'nullable|exists:courses,id',
            'batch_id' => 'nullable|exists:batches,id',
            'student_id' => 'nullable|exists:students,id',
            'is_private' => 'nullable|boolean',
            'media_file' => 'required|file|max:51200', // 50MB max
        ]);

        $file = $request->file('media_file');
        $mime = $file->getClientMimeType();
        $fileType = 'document';

        if (str_contains($mime, 'image/')) {
            $fileType = 'image';
        } elseif (str_contains($mime, 'video/')) {
            $fileType = 'video';
        }

        $folder = 'media/' . strtolower(str_replace(' ', '_', $request->category));
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs($folder, $fileName, 'public');

        $media = MediaFile::create([
            'title' => $request->title,
            'category' => $request->category,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $fileType,
            'file_size' => $file->getSize(),
            'course_id' => $request->course_id,
            'batch_id' => $request->batch_id,
            'student_id' => $request->student_id,
            'uploaded_by' => auth()->id(),
            'is_private' => $request->has('is_private'),
        ]);

        ActivityLog::record(
            'Upload Media',
            'Media',
            $media->id,
            "Uploaded media item '{$media->title}' in category {$media->category}."
        );

        return back()->with('success', 'Media file uploaded successfully.');
    }

    public function destroy($id)
    {
        $media = MediaFile::findOrFail($id);

        if (Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }

        $title = $media->title;
        $media->delete();

        ActivityLog::record('Delete Media', 'Media', $id, "Deleted media item '{$title}'.");

        return back()->with('success', "Media '{$title}' removed.");
    }
}
