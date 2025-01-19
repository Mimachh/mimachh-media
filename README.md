## Add a convert to webp
  
  public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'medias' => 'required|array',
            'medias.*' => 'file|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $product = Product::create($request->except('medias'));

        foreach ($request->file('medias') as $file) {
            Media::createMedia(
                $file,
                $product,
                auth()->id(),
                true,  // isActive
                false, // isPrivate
                'custom/path/to/store' // Optional custom path
            );
        }

        return redirect()->route('products.index');
    }


     public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'medias' => 'array',
            'medias.*' => 'file|mimes:jpeg,png,jpg,gif,svg',
      
        ]);

        $product->update($request->except(['medias', 'oldMedias']));


        foreach ($request->file('medias') as $file) {
            Media::createMedia(
                $file,
                $product,
                auth()->id(),
                true,  // isActive
                false, // isPrivate
                'custom/path/to/store' // Optional custom path
            );
        }

        return redirect()->route('products.index');
    }


    ## CREATE

    import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input'
import { useForm } from '@inertiajs/react'
import { CameraIcon, Upload, X } from 'lucide-react'
import React, { ChangeEvent } from 'react'


interface Attachment {
    mime?: string | null;
    type?: string | null;
}

const Create = () => {
    const { data, setData, post } = useForm({
        name: '',
        medias: [] as File[],
    })

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault()
        post(route('products.store'), {
            preserveScroll: true,
            onSuccess: () => {
                console.log('Product created')
            },
            onError: (e) => {
                console.log(e)
            }
        })
    }
    const resetMedia = () => {
        setFiles((prevFiles) => prevFiles.filter((file) => file.id !== null));
        // setDisplayButton(false);
    };

    const saveMedia = () => {
        data.medias = files.map((file) => file.file);
        // post(
        //     route("dashboard.media.update", {
        //         restaurant: restaurant.id,
        //         data,
        //     }),
        //     {
        //         preserveScroll: true,
        //         onSuccess: () => {
        //             setDisplayButton(false);
        //             setFiles([]);
        //             toast.success("Bannière mise à jour avec succès");
        //         },
        //         onError: () => {
        //             // console.log(error);
        //             toast.error("Une erreur est survenue");
        //         },
        //     }
        // );
    };

    const cancelMedia = (index: number) => {
        setFiles((prevFiles) => prevFiles.filter((file, i) => i !== index));
    };
    const [files, setFiles] = React.useState<any[]>([]);

    const isImage = (attachment: Attachment): boolean => {
        let mime = attachment.mime || attachment.type;
        if (mime) {
            mime = mime.split("/")[0];
            return mime.toLowerCase() === "image";
        }
        return false;
    };

    async function readFile(file: File): Promise<string | null> {
        return new Promise((resolve, reject) => {
            if (isImage(file)) {
                const reader = new FileReader();
                reader.onload = () => {
                    resolve(reader.result as string);
                };
                reader.onerror = reject;
                reader.readAsDataURL(file);
            } else {
                resolve(null);
            }
        });
    }

    const handleMedia = async (event: ChangeEvent<HTMLInputElement>) => {
        resetMedia()
        if (event.target.files) {
            const filesArray = Array.from(event.target.files); // Convertir FileList en tableau
            const filesAsStrings = await Promise.all(
                filesArray.map((file) => readFile(file)) // Lire chaque fichier
            );
    
            const newFiles = filesArray.map((file, index) => ({
                id: null,
                src: filesAsStrings[index],
                file: file,
            }));
    
            setFiles((prevFiles) => [...prevFiles, ...newFiles]);
        }
        event.target.value = ""; // Réinitialiser l'input
    };

    return (
        <div>
            <form onSubmit={submit}>
                <Input
                    placeholder='Name'
                    value={data.name}
                    onChange={(e) => setData('name', e.target.value)}
                />
                <div className="flex items-center justify-center">
                    <div className="flex justify-center border border-dashed border-gray-900/25 min-h-36 h-full w-full items-center">
                        <div className="text-center w-full">
                            <CameraIcon
                                className="mx-auto h-8 w-8 text-gray-300"
                                aria-hidden="true"
                            />
                            <div className="mt-2 flex text-sm leading-6 text-gray-600 text-center">
                                <label
                                    htmlFor="file-upload"
                                    className="flex items-center justify-center w-full relative cursor-pointer rounded-md  font-semibold"
                                >
                                    <Upload className="w-7 h-7 text-primaryBlue mb-2" />
                                    <input
                                        onChange={(e) => {
                                            handleMedia(e);
                                            setData('medias', e.target.files ? Array.from(e.target.files) : [])
                                        }}
                                        // onChange={(e) => {
                                        //     setData('medias', e.target.files)
                                        // }}
                                        id="file-upload"
                                        name="file-upload"
                                        type="file"
                                        className="sr-only"
                                        multiple={true}
                                    />
                                </label>
                            </div>
                            <p className="text-xs flex-wrap text-center leading-5 text-muted-foreground">
                                PNG, JPG, WEBP jusqu'à 1Go
                            </p>
                        </div>
                    </div>
                </div>

                <Button type='submit'>Save</Button>

             <div className='flex flex-wrap gap-3 w-full max-w-3xl mx-auto mt-4 justify-center'>
                   {files &&
                    files.map((file, index) => {
                        const attachmentErrorKey =
                            `attachments.${index}` as keyof Partial<
                                Record<"attachments", string>
                            >;
                        return (
                            <div key={index} className="h-auto w-32 relative">
                                <img
                                    src={file.src}
                                    alt={""}
                                    className="aspect-auto w-full h-full object-contain"
                                />

                                <X
                                    onClick={() => {
                                        cancelMedia(index);
                                    }}
                                    className="text-white hover:bg-muted-foreground/90 bg-muted-foreground cursor-pointer w-4 h-4 absolute top-0 right-0"
                                />



                            </div>
                        );
                    })}
             </div>
            </form>
        </div>
    )
}

export default Create



## Update import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input'
import { useForm, usePage } from '@inertiajs/react'
import { CameraIcon, Upload, X } from 'lucide-react'
import React, { ChangeEvent } from 'react'


interface Attachment {
    mime?: string | null;
    type?: string | null;
}

const Create = () => {
    const product = usePage().props.product as any;

    const { data, setData, post } = useForm({
        name: product.name,
        medias: [] as File[],
        oldMedias: product.medias,
    })

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault()
  
        post(route('products.update', {
            product: product
        }), {
            preserveScroll: true,
            onSuccess: () => {
                console.log('Product created')
            },
            onError: (e) => {
                console.log(e)
            }
        })
    }
    const resetMedia = () => {
        setFiles((prevFiles) => prevFiles.filter((file) => file.id !== null));
        // setDisplayButton(false);
    };
  const handleMedia = async (event: ChangeEvent<HTMLInputElement>) => {
        resetMedia()
        if (event.target.files) {
            const filesArray = Array.from(event.target.files); // Convertir FileList en tableau
            const filesAsStrings = await Promise.all(
                filesArray.map((file) => readFile(file)) // Lire chaque fichier
            );
    
            const newFiles = filesArray.map((file, index) => ({
                id: null,
                src: filesAsStrings[index],
                file: file,
            }));
    
            setFiles((prevFiles) => [...prevFiles, ...newFiles]);
        }
        event.target.value = ""; // Réinitialiser l'input
    };

    const cancelMedia = (index: number) => {
        setFiles((prevFiles) => prevFiles.filter((file, i) => i !== index));
    };
    const [files, setFiles] = React.useState<any[]>([]);

    const isImage = (attachment: Attachment): boolean => {
        let mime = attachment.mime || attachment.type;
        if (mime) {
            mime = mime.split("/")[0];
            return mime.toLowerCase() === "image";
        }
        return false;
    };

    async function readFile(file: File): Promise<string | null> {
        return new Promise((resolve, reject) => {
            if (isImage(file)) {
                const reader = new FileReader();
                reader.onload = () => {
                    resolve(reader.result as string);
                };
                reader.onerror = reject;
                reader.readAsDataURL(file);
            } else {
                resolve(null);
            }
        });
    }

    return (
        <div>
            <form onSubmit={submit}>
                <Input
                    placeholder='Name'
                    value={data.name}
                    onChange={(e) => setData('name', e.target.value)}
                />
                <div className="flex items-center justify-center">
                    <div className="flex justify-center border border-dashed border-gray-900/25 min-h-36 h-full w-full items-center">
                        <div className="text-center w-full">
                            <CameraIcon
                                className="mx-auto h-8 w-8 text-gray-300"
                                aria-hidden="true"
                            />
                            <div className="mt-2 flex text-sm leading-6 text-gray-600 text-center">
                                <label
                                    htmlFor="file-upload-2"
                                    className="flex items-center justify-center w-full relative cursor-pointer rounded-md  font-semibold"
                                >
                                    <Upload className="w-7 h-7 text-primaryBlue mb-2" />
                                    <input
                                        onChange={(e) => {
                                            handleMedia(e);
                                            console.log(e.target.files)
                                            setData('medias', e.target.files ? Array.from(e.target.files) : [])
                                        }}
                                        // onChange={(e) => {
                                        //     setData('medias', e.target.files)
                                        // }}
                                        id="file-upload-2"
                                        name="file-upload-2"
                                        type="file"
                                        className="sr-only"
                                        multiple={true}
                                    />
                                </label>
                            </div>
                            <p className="text-xs flex-wrap text-center leading-5 text-muted-foreground">
                                PNG, JPG, WEBP jusqu'à 1Go
                            </p>
                        </div>
                    </div>
                </div>

                <Button type='submit'>Save</Button>

             <div className='flex flex-wrap gap-3 w-full max-w-3xl mx-auto mt-4 justify-center'>
                   {files &&
                    files.map((file, index) => {
                        const attachmentErrorKey =
                            `attachments.${index}` as keyof Partial<
                                Record<"attachments", string>
                            >;
                        return (
                            <div key={index} className="h-auto w-32 relative">
                                <img
                                    src={file.src}
                                    alt={""}
                                    className="aspect-auto w-full h-full object-contain"
                                />

                                <X
                                    onClick={() => {
                                        cancelMedia(index);
                                    }}
                                    className="text-white hover:bg-muted-foreground/90 bg-muted-foreground cursor-pointer w-4 h-4 absolute top-0 right-0"
                                />



                            </div>
                        );
                    })}
             </div>
            </form>

            <div className='flex flex-wrap gap-3 w-full max-w-3xl mx-auto mt-4 justify-center'>
                {data?.oldMedias && data?.oldMedias.map((media, index) => (
                    <div key={index} className="h-auto w-32 relative">
                    
                      <img src={media.path} alt="" className="aspect-auto w-full h-full object-contain" />
                      <X onClick={() => cancelMedia(index)} className="text-white hover:bg-muted-foreground/90 bg-muted-foreground cursor-pointer w-4 h-4 absolute top-0 right-0" />
                    
                    </div>
                ))}
            </div>
        </div>
    )
}

export default Create
