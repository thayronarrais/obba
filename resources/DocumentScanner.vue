<script setup lang="ts">
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onUnmounted, ref } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { UserRole, InvoiceType } from '@/enums/enums';
import AppLayout from '@/layouts/AppLayout.vue';
import { SEPARATOR, KEY_VALUE_SEPARATOR, invoiceSpecifications, invoiceCategories } from '@/constants/invoice';
import type { DetectedBarcode } from 'barcode-detector';
import { QrcodeStream } from 'vue-qrcode-reader';
import { Cropper } from 'vue-advanced-cropper'
import UPNG from 'upng-js';
import 'vue-advanced-cropper/dist/style.css';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectSeparator, SelectTrigger, SelectValue } from '@/components/ui/select';
import Icon from '@/components/Icon.vue';



interface ConstraintOption {
	label: string;
	constraints: MediaTrackConstraints;
}

interface InvoiceData {
	[key: string]: string;
}

interface Company {
	id: number;
	name: string;
	nif: string;
	address?: string;
}

interface PageProps {
	company: Company;
	[key: string]: any;
}

const breadcrumbs: BreadcrumbItem[] = [
	{
		title: 'Ler Código QR',
		href: route('scanner'),
	},
];

const page = usePage<PageProps>();
const companyId = computed(() => page.props.company?.id);
const userRole = computed(() => page.props.auth?.user.role);

const capturingInvoice = ref(false);
const preValidationForm = useForm({
	invoiceAtcud: '',
	invoiceNif: '',
	companyNif: page.props.company?.nif,
});
const invoiceTypesRef = ref('1');
const invoiceCategoriesRef = ref('');
const form = useForm({
	invoiceAtcud: '',
	invoiceData: {} as InvoiceData,
	invoiceImage: null as File | null,
	invoiceType: 0,
	invoiceCategory: 0,
	invoiceCompanyId: companyId.value,
});

/*** detection handling ***/
const error = ref('')
const result = ref('');
const camReady = ref(false);
const camPaused = ref(false);

function isValidQrCode(str: string) {
	const [a, b] = str.split('*');
	const regexA = /A:\d{9}/;
	const regexB = /B:\d{9}/;
	return regexA.test(a) && regexB.test(b);
}

function onDetect(detectedCodes: DetectedBarcode[]) {
	preValidationForm.reset();
	result.value = detectedCodes.map((code) => code.rawValue).join('');
	if (result.value.endsWith('*')) {
		result.value = result.value.slice(0, -1);
	}

	if (isValidQrCode(result.value)) {
		camReady.value = false;
		camPaused.value = true;

		form.invoiceData = Object.fromEntries(result.value.split(SEPARATOR).map(e => e.split(KEY_VALUE_SEPARATOR)));

		// Whenever this check makes sense/is more comprehensive, will enable it, if ever
		// const documentType = form.invoiceData.D ?? '';
		// if (!(Object.keys(invoiceDocumentTypes).includes(documentType))) {
		// 	error.value = "Tipo de documento não aceite.";
		// 	return;
		// }

		preValidationForm.invoiceNif = form.invoiceData.B ?? '';
		if (page.props.company.nif !== preValidationForm.invoiceNif) {
			error.value = "O NIF do adquirente deve corresponder ao NIF da empresa.";
			return;
		}

		preValidationForm.invoiceAtcud = form.invoiceData.H ?? '';
		preValidationForm.post(route('invoice.exists'), {
			preserveState: true,
			onSuccess: () => {
				form.invoiceAtcud = preValidationForm.invoiceAtcud;
			},
			onError: () => {
				form.reset();
			}
		});
	}
}

/*** select camera ***/
const cameraSelectRef = ref('environment');
const selectedConstraints = ref<MediaTrackConstraints>({ facingMode: cameraSelectRef.value, width: { ideal: 1920 }, height: { ideal: 1080 } });
const constraintOptions = ref<ConstraintOption[]>([]);

const stream = ref<MediaStream | null>(null);
const torchOn = ref<boolean>(false);
const torchSupported = ref<boolean>(false);
const torchError = ref();

async function isTorchSupported(track: MediaStreamTrack): Promise<boolean> {
	try {
		await track.applyConstraints({ advanced: [{ torch: false }] } as any);
		const settings = track.getSettings();
		if ('torch' in settings) {
			// Reset torch to off initially
			// await track.applyConstraints({ advanced: [{ torch: false }] } as any);
			return true;
		}
		return false;
	} catch (error) {
		torchError.value = error;
		return false;
	}
};

async function toggleTorch(state: string | null = null) {
	if (!stream.value || !torchSupported.value) return;
	const track = stream.value.getVideoTracks()[0];
	let newTorchState = false;
	if (state) {
		newTorchState = state === 'on';
	} else {
		newTorchState = !torchOn.value;
	}

	try {
		await track.applyConstraints({ advanced: [{ torch: newTorchState }] } as any);
		torchOn.value = newTorchState; // Update state only on success

	} catch (error) {
		torchError.value = error;
	}
};

async function onCameraReady() {
	// NOTE: on iOS we can't invoke `enumerateDevices` before the user has given
	// camera access permission. `QrcodeStream` internally takes care of
	// requesting the permissions. The `camera-on` event should guarantee that this
	// has happened.
	stream.value = await navigator.mediaDevices.getUserMedia({
		video: { facingMode: 'environment' },
	});
	const track = stream.value.getVideoTracks()[0];
	torchSupported.value = await isTorchSupported(track);
	const devices = await navigator.mediaDevices.enumerateDevices();
	const videoDevices = devices.filter(({ kind }) => kind === 'videoinput');

	constraintOptions.value = [
		...videoDevices.map(({ deviceId, label }) => ({
			label: `${label}`,
			// label: `${label} (ID: ${deviceId})`,
			constraints: {
				deviceId,
				width: { ideal: 1920 },
				height: { ideal: 1080 }
			}
		})),
	];

	camReady.value = true;
	error.value = ''
}

/*** error handling ***/
function onError(err: { name: string; message: string; }) {
	error.value = `[${err.name}]: `

	if (err.name === 'NotAllowedError') {
		error.value += 'you need to grant camera access permission'
	} else if (err.name === 'NotFoundError') {
		error.value += 'no camera on this device'
	} else if (err.name === 'NotSupportedError') {
		error.value += 'secure context required (HTTPS, localhost)'
	} else if (err.name === 'NotReadableError') {
		error.value += 'is the camera already in use?'
	} else if (err.name === 'OverconstrainedError') {
		error.value += 'installed cameras are not suitable'
	} else if (err.name === 'StreamApiNotSupportedError') {
		error.value += 'Stream API is not supported in this browser'
	} else if (err.name === 'InsecureContextError') {
		error.value +=
			'Camera access is only permitted in secure context. Use HTTPS or localhost rather than HTTP.'
	} else {
		error.value += err.message
	}
}

const isCapturing = ref(false);
const imageDataUrl = ref('');

function captureImage() {
	const canvas = document.getElementById('qrcode-stream-tracking-layer') as HTMLCanvasElement | null;
	const video = document.querySelector('#img-capture video') as HTMLVideoElement | null;

	if (canvas && video) {
		const context = canvas.getContext('2d');

		if (context) {
			canvas.width = video.videoWidth;
			canvas.height = video.videoHeight;
			context.drawImage(video, 0, 0, canvas.width, canvas.height);
			imageDataUrl.value = canvas.toDataURL('image/png');
			const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
			const png = UPNG.encode([imageData.data.buffer], canvas.width, canvas.height, 32);
			const blob = new Blob([png], { type: 'image/png' });
			form.invoiceImage = new File([blob], `temp-invoice-${Date.now()}.png`, { type: 'image/png' });
			camPaused.value = true;
			capturingInvoice.value = false;
			toggleTorch('off');
			isCapturing.value = false;
		}
	}
}

const cropperRef = ref<InstanceType<typeof Cropper> | null>(null);
const isCropping = ref(false);
const imageIsCropped = ref(false);

function cropImage() {
	isCropping.value = true;
}

function confirmCrop() {
	if (cropperRef.value) {
		const { canvas } = cropperRef.value.getResult();
		if (canvas) {
			imageDataUrl.value = canvas.toDataURL();
			const context = canvas.getContext('2d');
			if (context) {
				const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
				const png = UPNG.encode([imageData.data.buffer], canvas.width, canvas.height, 32);
				const blob = new Blob([png], { type: 'image/png' });
				form.invoiceImage = new File([blob], `temp-invoice-${Date.now()}.png`, { type: 'image/png' });
			}
			isCropping.value = false;
			imageIsCropped.value = true;
		}
	}
}

function cancelCrop() {
	isCropping.value = false;
}



const categoryError = ref(false);

function saveInvoice() {
	form.invoiceType = Number(invoiceTypesRef.value) ?? 0;
	form.invoiceCategory = Number(invoiceCategoriesRef.value) ?? 0;
	form.post(route('invoice.store'), {
		forceFormData: true,
		onSuccess: () => {
			form.reset();
			result.value = '';
			imageDataUrl.value = '';
			camPaused.value = false;
			imageIsCropped.value = false;
			categoryError.value = false;
			capturingInvoice.value = false;
			autoClearFlash();
			toggleTorch('off');
		},
		onError: (errors) => {
			console.error('Upload failed', errors);
		}
	});
}

function viewInvoice(id: any) {
	return router.get(route('invoice.show', { id }));
}

function cleanupDeviceTracks(streamVal: MediaStream) {
	const tracks = streamVal.getTracks();
	tracks?.forEach(track => track.stop());
}

onBeforeUnmount(() => {
	if (stream.value) {
		cleanupDeviceTracks(stream.value);
	}
});

onUnmounted(() => {
	if (stream.value) {
		cleanupDeviceTracks(stream.value);
	}
});
</script>

<template>

	<Head title="Ler Código QR" />

	<AppLayout :breadcrumbs="breadcrumbs">
		<div class="flex h-full flex-1 flex-col gap-4">
			<div v-if="userRole === UserRole.ADMIN || userRole === UserRole.USER" class="relative flex-1 md:min-h-min">
				<div v-if="page.props.company" class="flex flex-col gap-4 w-full h-full">
					<div v-if="!camReady && !camPaused" class="absolute flex justify-center items-center left-0 top-0 w-full h-full bg-black rounded-xl text-center p-6 font-bold text-3xl">
						<h2>
							A iniciar dispositivos...
						</h2>
					</div>
					<p v-if="error" class="px-4 pt-4 text-destructive text-wrap w-full">{{ error }}</p>
					<!-- QR code scan -->
					<div v-if="!camPaused && !capturingInvoice" class="max-h-[calc(100vh-4rem)]">
						<qrcode-stream :constraints="(selectedConstraints as any)" :formats="['qr_code']" :paused="camPaused" @error="onError" @detect="onDetect" @camera-on="onCameraReady">
							<h3 v-if="camReady" class="mt-4 text-center text-2xl font-bold drop-shadow">
								Ler código QR
							</h3>
							<div class="absolute left-2 top-2">
								<Select v-model="cameraSelectRef">
									<SelectTrigger class="size-6 flex justify-center items-center bg-popover" />
									<SelectContent>
										<SelectGroup>
											<SelectLabel>Selecionar dispositivo de vídeo</SelectLabel>
											<template v-for="option in constraintOptions" :key="option.label">
												<SelectItem :value="option.constraints.toString()">
													{{ option.label }}
												</SelectItem>
											</template>
										</SelectGroup>
									</SelectContent>
								</Select>
							</div>
						</qrcode-stream>
					</div>
					<!-- Show invoice data / details -->
					<div v-if="result && !isCropping && !capturingInvoice && !imageDataUrl && camPaused" class="flex flex-col gap-4">
						<div v-if="!isValidQrCode(result)" class="flex flex-col gap-4 text-lg font-bold">
							<span>
								Código QR não é uma fatura ou não tem contribuinte associado
							</span>
							<div>
								<Button variant="secondary" @click="camPaused = false; result = ''; form.invoiceData = {}">
									Ler código QR
								</Button>
							</div>
						</div>
						<div v-else class="px-4 pb-4 max-w-md border-t pt-2">
							<div v-if="!capturingInvoice && !form.processing && !form.hasErrors && !preValidationForm.processing && !preValidationForm.hasErrors" class="flex flex-col gap-4 max-w-xl">
								<ul class="text-wrap w-full break-all grid gap-2">
									<li v-for="(value, key, idx) in form.invoiceData" :key="idx" class="border-b border-white/50 text-xs">
										<div class="grid grid-cols-2 gap-4">
											<div>
												{{ invoiceSpecifications[key] ? invoiceSpecifications[key].description : `key: ${key}, value: ${value}` }}
											</div>
											<div>
												{{ value }}
											</div>
										</div>
									</li>
								</ul>
								<div class="flex gap-4">
									<Button variant="secondary" @click="camPaused = false; result = ''; form.invoiceData = {}">
										Ler código QR
									</Button>
									<Button v-if="!error" @click="capturingInvoice = true; camPaused = false">
										Fotografar Fatura
									</Button>
								</div>
							</div>
						</div>
					</div>
					<!-- Photo mode grid overlay and image capturing - using qr-code-scanner, but only to capture the invoice -->
					<div v-if="!camPaused && capturingInvoice" id="img-capture" class="relative w-full mx-auto my-0 max-h-[calc(100svh-10rem)]">
						<qrcode-stream :constraints="(selectedConstraints as any)" :paused="camPaused" @error="onError" @camera-on="onCameraReady" />
						<div v-if="camReady && !isCapturing">
							<div class="grid-overlay">
								<div class="grid-cell"></div>
								<div class="grid-cell"></div>
								<div class="grid-cell"></div>
								<div class="grid-cell"></div>
								<div class="grid-cell"></div>
								<div class="grid-cell"></div>
								<div class="grid-cell"></div>
								<div class="grid-cell"></div>
								<div class="grid-cell"></div>
							</div>
							<div class="flex gap-4 justify-center items-center absolute w-full bottom-6 left-0">
								<button @click="isCapturing = true; captureImage()" class="rounded-full bg-accent/60 border border-primary p-3">
									<Icon name="Camera" class="size-10" />
								</button>
								<button v-if="torchSupported" @click="toggleTorch()" class="absolute right-6 rounded-full bg-accent/60 border border-primary p-3">
									<Icon :name="torchOn ? 'FlashlightOff' : 'Flashlight'" class="size-8" />
								</button>
							</div>
						</div>
						<div v-if="isCapturing" class="absolute flex justify-center items-center left-0 top-0 w-full h-full bg-black/60 text-secondary font-bold text-3xl">
							<h2>
								A processar...
							</h2>
						</div>
					</div>
					<!-- Show captured image -->
					<div v-if="imageDataUrl && !isCropping" class="flex flex-col gap-4 w-full h-full px-4 max-h-[calc(100vh-4rem)]">
						<img class="w-full max-w-3xl h-fit max-h-[512px] object-contain" :src="imageDataUrl" alt="Captured Invoice">
						<div class="flex flex-wrap gap-4">
							<Button variant="secondary" @click="camPaused = false; imageDataUrl = ''; capturingInvoice = true">
								Repetir foto
							</Button>
							<Button v-if="!imageIsCropped" variant="secondary" @click="cropImage">
								Ajustar Imagem
							</Button>
							<!-- <Select v-model="invoiceTypesRef">
								<SelectTrigger class="w-[180px]">
									<SelectValue placeholder="Selecionar tipo" />
								</SelectTrigger>
								<SelectContent>
									<SelectGroup>
										<SelectLabel>Despesa ou Venda</SelectLabel>
										<template v-for="(type, key) in invoiceTypes" :key="key">
											<SelectItem :value="key">
												{{ type }}
											</SelectItem>
										</template>
									</SelectGroup>
								</SelectContent>
							</Select> -->
							<Select v-if="invoiceTypesRef === String(InvoiceType.EXPENSE)" v-model="invoiceCategoriesRef">
								<SelectTrigger class="w-[180px]">
									<SelectValue placeholder="Selecionar categoria" />
								</SelectTrigger>
								<SelectContent>
									<SelectLabel>Categorias</SelectLabel>
									<template v-for="[key, category] in invoiceCategories" :key="key">
										<template v-if="Number(key) > 99">
											<SelectSeparator />
											<SelectLabel>{{ category }}</SelectLabel>
										</template>
										<SelectItem v-else :value="key">
											{{ category }}
										</SelectItem>
									</template>
								</SelectContent>
							</Select>
							<Button v-if="invoiceCategoriesRef || invoiceTypesRef === String(InvoiceType.SALE)" @click="saveInvoice">
								Guardar Fatura
							</Button>
						</div>
						<div v-if="categoryError" class="">
							Categoria inválida. Selecionar uma categoria da lista.
						</div>
					</div>
					<!-- Image Cropping -->
					<div v-if="isCropping" class="relative w-full h-full max-w-[100vw] max-h-[calc(100vh-4rem)]">
						<cropper ref="cropperRef" :src="imageDataUrl" :auto-zoom="true" :transitions="true" image-restriction="fill-area" default-boundaries="fill" class="cropper" />
						<div class="flex flex-col gap-4 absolute left-4 bottom-4">
							<button @click="cancelCrop" class="rounded-full bg-accent/60 border border-destructive p-3">
								<Icon name="X" class="size-9 text-destructive" />
							</button>
							<button @click="confirmCrop" class="rounded-full bg-accent/60 border border-chart-2 p-3">
								<Icon name="Check" class="size-9 text-chart-2" />
							</button>
						</div>
					</div>
					<div v-if="form.hasErrors || preValidationForm.hasErrors" class="text-lg font-semibold text-destructive pl-4">
						<div v-if="form.hasErrors">
							{{Object.values(form.errors).map(val => Object.keys(form.errors).length > 1 ? '- ' : '' + `${val}\n`).join('')}}
						</div>
						<div v-else>
							{{Object.values(preValidationForm.errors).map(val => Object.keys(preValidationForm.errors).length > 1 ? '- ' : '' + `${val}\n`).join('')}}
						</div>
						<div v-if="page.props.errors?.invoiceAtcud || page.props.errors?.invoiceNif" class="text-foreground mt-4">
							<Link :href="route('scanner')" class="flex items-center gap-x-2">
							<Icon name="ChevronLeft" class="size-6" /> Voltar
							</Link>
						</div>
					</div>
				</div>
				<div v-else>
					<div class="absolute flex flex-col gap-4 justify-center items-center left-0 top-0 w-full h-full bg-background rounded-xl text-center p-6">
						<h2 class="font-bold text-3xl">
							Deve definir os dados da empresa
						</h2>
						<Link :href="route('company')" class="flex items-center gap-x-2 underline">
						Configurar Empresa
						</Link>
					</div>
				</div>
			</div>
			<div v-else>
				<div class="absolute flex flex-col gap-4 justify-center items-center left-0 top-0 w-full h-full bg-background rounded-xl text-center p-6">
					<h2 class="font-bold text-3xl">
						Não é possível apresentar esta página
					</h2>
				</div>
			</div>
		</div>
		<Transition v-on:vue:mounted="autoClearFlash()" name="slide-fade">
			<div v-if="flash && flash.text" @click="clearFlash" class="fixed font-medium flex flex-col justify-center items-start right-6 top-6 min-w-64 rounded-lg border p-4 w-fit h-fit" :class="{ 'text-foreground bg-chart-2': flash.success, 'text-foreground bg-destructive': flash.success === false }">
				<p>
					{{ flash.text }}
				</p>
				<Button v-if="flash.invoiceId" @click="viewInvoice(flash.invoiceId)">
					Ver documento
				</Button>
			</div>
		</Transition>
	</AppLayout>
</template>