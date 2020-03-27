<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Google\Cloud\Core\ServiceBuilder;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;

class AIController extends Controller
{
    public function index(){
        return view('run');
    }
    public function snap(Request $request){
        $image = $request->image;  // your base64 encoded
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = str_random(10).'.'.'jpg';
        $imageAnnotator = new ImageAnnotatorClient(
            [
                'credentials' => base_path('fda.json')
            ]
        );
    $response = $imageAnnotator->faceDetection(base64_decode($image));
    $faces = $response->getFaceAnnotations();
    $fo = count($faces) != 0 ? 1 : 0 ;
    return response()->json(['response' => trans('true'),'message'=>'Face found: '.$fo]);
    }
    public function detectFaces(){
        $cloud = new ServiceBuilder([
            'keyFilePath' => base_path('fda.json'),
            'projectId' => 'facial-detection-app'
        ]);

        $vision = $cloud->vision();

        $output = imagecreatefromjpeg(public_path('sekolah.jpg'));
        $image = $vision->image(file_get_contents(public_path('sekolah.jpg')), ['FACE_DETECTION']);
        $results = $vision->annotate($image);
        
        //dd($results->faces());

        foreach ($results->faces() as $face) {
            $vertices = $face->boundingPoly()['vertices'];

            $x1 = $vertices[0]['x'];
            $y1 = $vertices[0]['y'];
            $x2 = $vertices[2]['x'];
            $y2 = $vertices[2]['y'];

            imagerectangle($output, $x1, $y1, $x2, $y2, 0x00ff00);
        }

        header('Content-Type: image/jpeg');

        imagejpeg($output);
        imagedestroy($output);
    }
    public function detectFacesV2($path, $outFile = null){
        $imageAnnotator = new ImageAnnotatorClient(
            [
                'credentials' => base_path('fda.json')
            ]
        );

    # annotate the image
    $path = public_path('office.jpg');
    $image = file_get_contents($path);
    $response = $imageAnnotator->faceDetection($image);
    $faces = $response->getFaceAnnotations();

    # names of likelihood from google.cloud.vision.enums
    $likelihoodName = ['UNKNOWN', 'VERY_UNLIKELY', 'UNLIKELY',
    'POSSIBLE', 'LIKELY', 'VERY_LIKELY'];

    printf("%d faces found:" . PHP_EOL, count($faces));
    foreach ($faces as $face) {
        $anger = $face->getAngerLikelihood();
        printf("Anger: %s" . PHP_EOL, $likelihoodName[$anger]);

        $joy = $face->getJoyLikelihood();
        printf("Joy: %s" . PHP_EOL, $likelihoodName[$joy]);

        $surprise = $face->getSurpriseLikelihood();
        printf("Surprise: %s" . PHP_EOL, $likelihoodName[$surprise]);

        # get bounds
        $vertices = $face->getBoundingPoly()->getVertices();
        $bounds = [];
        foreach ($vertices as $vertex) {
            $bounds[] = sprintf('(%d,%d)', $vertex->getX(), $vertex->getY());
        }
        print('Bounds: ' . join(', ', $bounds) . PHP_EOL);
        print(PHP_EOL);
    }

    }
}
