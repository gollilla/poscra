<div class="carousel-item sm:mx-[12px] mx-[5px] cursor-pointer hover:opacity-70 duration-500">
    <img onclick="edit_modal_{{ str_replace('-', '_', $post->id) }}.showModal()" wire:click="init" class="sm:w-[330px] sm:h-[185px] rounded-[20px] w-[178px] h-[100px]" src="{{ Storage::url($post->attachment->thumbnail) }}" />
    <dialog id="edit_modal_{{ str_replace('-', '_', $post->id) }}" class="modal" wire:ignore.self>
        <div class="modal-box max-w-[1190px] bg-mywhite">
            <form class="flex flex-col md:flex-row md:py-5 md:px-10 md:gap-[24px] gap-[10px]" wire:submit.prevent="save">
                <div class="md:w-3/5 w-full relative group">
                    @if ($attachment || $attachment_path)
                        <div class="absolute z-10 top-[10px] right-[10px] group-hover:block hidden">
                            <button type="button" class="btn btn-square text-error bg-mywhite min-h-0" wire:click="clearAttachment">
                                <i class="fa-regular fa-trash-can text-xl"></i>
                            </button>
                        </div>
                    @endif
                    @if ($attachment_type === 'image')
                        <div>
                            @if ($attachment)
                                <img id="preview_image_{{ str_replace('-', '_', $post->id) }}" class="object-contain h-[220px] md:h-[430px]" src="{{ $attachment->temporaryUrl() }}" />
                            @else
                                <img id="preview_image_{{ str_replace('-', '_', $post->id) }}" class="object-contain h-[220px] md:h-[430px]" src="{{ Storage::url($attachment_path) }}" />
                            @endif
                        </div>
                    @endif
                    <label id="dropzone_{{ str_replace('-', '_', $post->id) }}" for="dropzone-file_{{ str_replace('-', '_', $post->id) }}" class="h-[220px] md:h-[430px] flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-mywhiet @if($attachment_path || $attachment) hidden @endif">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">サムネイル用 PNG, JPG or GLB</p>
                        </div>
                    </label>
                    <div class="absolute z-10 bottom-[10px] left-[10px] @if($attachment_type !== '3dmodel') hidden @endif">
                        <button type="button" class="btn bg-mywhite disabled:opacity-75 disabled:!bg-mywhite font-yusei text-mydark" id="set_thumbnail_{{ str_replace('-', '_', $post->id) }}" wire:click="setThumbnail" wire:ignore.self>サムネイルとして設定</button>
                    </div>
                    <div id="main_canvas_{{ str_replace('-', '_', $post->id) }}" class="w-full h-[220px] md:h-[430px] @if($attachment_type !== '3dmodel') hidden @endif">
                        <canvas id="mcstructure_preview_{{ str_replace('-', '_', $post->id) }}" class="" wire:ignore.self></canvas>
                    </div>
                    <input id="dropzone-file_{{ str_replace('-', '_', $post->id) }}" type="file" class="absolute h-[1px] w-[1px] opacity-0 top-[50%] left-[50%]" wire:model="attachment" name="attachment" />
                    <input id="thumbnail_image_{{ str_replace('-', '_', $post->id) }}" type="file" name="thumbnail_image" wire:model="thumbnail" class="hidden" />
                </div>
                <div class="flex flex-col font-yusei text-mydark md:w-2/5 w-full">
                    <div class="flex items-end">
                        <label for="title" class="text-[] mt-5">タイトル</label>
                        @error('title')<span class="text-error">{{ $message }}</span>@enderror
                    </div>
                    <input class="w-full bg-mywhite border-mydark input input-bordered" id="title" name="title" type="text" wire:model="title" maxlength="20" required/>

                    <div class="flex items-end">
                        <label for="description" class="text-[] mt-3">説明</label>
                        @error('descrption')<span class="text-error">{{ $message }}</span>@enderror
                    </div>
                    <textarea class="textarea textarea-bordered w-full bg-mywhite border-mydark h-[90px]" id="description" name="description" maxlength="100" wire:model="description"></textarea>
                    <div class="flex items-end">
                        <label for="structure_{{ str_replace('-', '_', $post->id) }}" class="text-[] mt-3">mcstructure</label>
                        <div class="tooltip" data-tip="*.mcstructureファイルはストラクチャーブロックによりエクスポートされたファイルです。 詳しい使い方の説明は省略します。">
                            <i class="fa-solid fa-circle-question mx-2 mb-1"></i>
                        </div>
                        @error('mcstructure_file_error')<span class="text-error">{{ $message }}</span>@enderror
                    </div>
                    <input type="file" class="file-input file-input-bordered w-full bg-mywhite input-mydark" id="structure_{{ str_replace('-', '_', $post->id) }}" name="structure"  wire:model="mcstructure" accept=".mcstructure"/>

                    <button id="edit_submit" wire:click="save" class="btn bg-mydark text-mywhite w-full text-[15px] font-yusei hover:bg-mydark/80 md:mt-auto mt-[20px]"><i class="fa-solid fa-paper-plane"></i>更新</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button wire:click="resetOnClose">close</button>
        </form>
    </dialog>
    <script>
        const dropzone_{{ str_replace('-', '_', $post->id) }} = document.getElementById("dropzone_{{ str_replace('-', '_', $post->id) }}");
        const targetInput_{{ str_replace('-', '_', $post->id) }} = document.getElementById("dropzone-file_{{ str_replace('-', '_', $post->id) }}");
        const thumbnailInput_{{ str_replace('-', '_', $post->id) }} = document.getElementById("thumbnail_image_{{ str_replace('-', '_', $post->id) }}"); // 
        const setThumbnail_{{ str_replace('-', '_', $post->id) }} = document.getElementById("set_thumbnail_{{ str_replace('-', '_', $post->id) }}"); // button 
        
        const canvas_{{ str_replace('-', '_', $post->id) }} = document.getElementById("mcstructure_preview_{{ str_replace('-', '_', $post->id) }}");

        dropzone_{{ str_replace('-', '_', $post->id) }}.addEventListener('dragover', (ev) => {
            ev.preventDefault();
            //console.log(ev);
        });

        dropzone_{{ str_replace('-', '_', $post->id) }}.addEventListener('dragleave', (ev) => {
            ev.preventDefault();
            //console.log(ev);
        });

        dropzone_{{ str_replace('-', '_', $post->id) }}.addEventListener('drop', (ev) => {
            ev.preventDefault();

            const files = ev.dataTransfer.files;
            targetInput.files = files;

            const changeEvent = new Event('change');
            targetInput.dispatchEvent(changeEvent);
            
            //console.log(targetInput.files);
        });

        window.addEventListener('setThumbnail_{{ str_replace('-', '_', $post->id) }}', (ev) => {
            //console.log(ev);
            setThumbnail_{{ str_replace('-', '_', $post->id) }}.disabled = true;
            canvas_{{ str_replace('-', '_', $post->id) }}.toBlob(function (blob) {
                const file = new File([blob], 'thumbnail.png', { type: "image/png" });
                const dt = new DataTransfer();
                dt.items.add(file);
                thumbnailInput_{{ str_replace('-', '_', $post->id) }}.files = dt.files;
                thumbnailInput_{{ str_replace('-', '_', $post->id) }}.dispatchEvent(new Event('change'));
            });
        });

        window.addEventListener('update_preview_{{ str_replace('-', '_', $post->id) }}', (ev) => {
            //console.log(canvas_{{ str_replace('-', '_', $post->id) }})
            const renderer = new THREE.WebGLRenderer({
                canvas: canvas_{{ str_replace('-', '_', $post->id) }},
                antialias: true,
                preserveDrawingBuffer: true,
            });
            // ウィンドウサイズ設定
            let width = document.getElementById("main_canvas_{{ str_replace('-', '_', $post->id) }}").getBoundingClientRect().width;
            let height = document.getElementById("main_canvas_{{ str_replace('-', '_', $post->id) }}").getBoundingClientRect().height;
            //console.log(width, height);
            renderer.setPixelRatio(1);
            renderer.setSize(width, height);
            //console.log(window.devicePixelRatio);
            //console.log(width + ", " + height);

            // シーンを作成
            const scene = new THREE.Scene();

            // カメラを作成
            const camera = new THREE.PerspectiveCamera(45, width / height, 1, 100000);
            camera.position.set(0, 3000, -12000);

            const controls = new OrbitControls(camera, document.getElementById('main_canvas_{{ str_replace('-', '_', $post->id) }}'));
            controls.addEventListener("change", function (ev) {
                setThumbnail_{{ str_replace('-', '_', $post->id) }}.disabled = false;
            });
            //camera.lookAt(new THREE.Vector3(0, 400, 0));

            // Load GLTF or GLB
            const loader = new GLTFLoader();
            const url = ev.detail.preview_url;

            //console.log(url);
            let model = null;
            loader.load(
                url,
                function (gltf) {
                    model = gltf.scene;
                    model.name = "model_with_cloth";
                    model.scale.set(400.0, 400.0, 400.0);
                    model.position.set(0, -5000, 0);
                    scene.add(gltf.scene);
                    // 初期化のために実行
                    onResize();
                },
                function (xhr) {
                    
                    console.log( ( xhr.loaded / xhr.total * 100 ) + '% loaded' );
                },
                function (error) {
                    //console.log('An error happened');
                    console.error(error);
                }
            );

            // 平行光源
            const light = new THREE.AmbientLight(0xFFFFFF, 5.0);
            /*light.intensity = 2; */
            light.position.set(1, 1, 1);
            // シーンに追加
            scene.add(light);

            // 初回実行
            tick();
            function tick() {
                controls.update();
                renderer.render(scene, camera);
                requestAnimationFrame(tick);
            }

            // リサイズイベント発生時に実行
            window.addEventListener('resize', onResize);
            function onResize() {
                // サイズを取得
                width = document.getElementById("main_canvas_{{ str_replace('-', '_', $post->id) }}").getBoundingClientRect().width;
                height = document.getElementById("main_canvas_{{ str_replace('-', '_', $post->id) }}").getBoundingClientRect().height;

                // レンダラーのサイズを調整する
                renderer.setPixelRatio(window.devicePixelRatio);
                renderer.setSize(width, height);

                // カメラのアスペクト比を正す
                camera.aspect = width / height;
                camera.updateProjectionMatrix();
                //console.log(width);
            }
        })
    </script>
</div>