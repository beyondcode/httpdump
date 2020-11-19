<html lang="en">
<head>
    <title>HTTP Dump - {{ $dump->name }}</title>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.5.17/dist/vue.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tailwindcss/ui@latest/dist/tailwind-ui.min.css">
    <script src="https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js"></script>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/gh/highlightjs/cdn-release@10.1.0/build/styles/github.min.css">
    <script src="//cdn.jsdelivr.net/gh/highlightjs/cdn-release@10.1.0/build/highlight.min.js" async></script>
    <script src="/js/app.js"></script>
    <style>
        .even\:bg-gray-50:nth-child(even) {
            background-color: #f7fafc;
        }
    </style>
</head>
<body>
<div id="app" class="">
    <div class="relative bg-indigo-600" style="marign-left: -1px">
        <div class="max-w-screen-xl mx-auto py-3 px-3 sm:px-6 lg:px-8">
            <div class="pr-16 sm:text-center sm:px-16">
                <p class="font-medium text-white flex justify-center">
                    <span class="inline-block">Waiting for requests on:
                        <a class="underline" target="_blank" href="{{ route('collect', [$dump->name]) }}">{{ route('collect', [$dump->name]) }}</a>
                    </span>
                </p>
            </div>
        </div>
    </div>
    <div class="p-5 flex flex-col md:flex-row">
        <div class="w-full md:w-1/3 flex flex-col mr-5">
            <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                <div class="flex mb-4">
                        <span class="h-8 inline-flex rounded-md shadow-sm">
                              <button @click.prevent="clearRequests"
                                      type="button"
                                      class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-xs leading-4 font-medium rounded text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150">
                                Clear
                              </button>
                        </span>

                    <div class="ml-4 flex-grow relative rounded-md shadow-sm">
                        <input class="h-8 form-input block w-full sm:text-sm sm:leading-5" v-model="search" placeholder="Search" />
                    </div>
                </div>
                <div
                    class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                    <table class="min-w-full">
                        <thead>
                        <tr>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                URL
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white">
                        <tr v-for="request in filteredRequests"
                            :class="{'bg-gray-100': currentRequest === request}"
                            @click="setRequest(request)">
                            <td class="cursor-pointer px-6 py-4 whitespace-normal border-b border-gray-200 text-sm leading-5 font-medium text-gray-900">
                                <p>
                                    @{ request.request.method }
                                    @{ request.request.uri }
                                </p>
                                <span class="text-xs">@{ request.subdomain }</span>
                                <span class="text-xs text-gray-600">@{ request.performed_at }</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="w-full md:w-2/3 mt-5 md:mt-0 md:ml-5">
            <div v-if="currentRequest" class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 flex">
                        @{ currentRequest.request.method } @{ currentRequest.request.uri }
                        <div class="flex-grow"></div>
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm leading-5 text-gray-500">
                        <span class="text-xs text-gray-600">Received at: @{ currentRequest.performed_at }</span>
                    </p>
                </div>

                <div>
                    <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex justify-between" v-if="Object.keys(currentRequest.request.query).length > 0">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Query Parameters
                        </h3>
                        <span class="inline-flex rounded-md shadow-sm ml-4">
                            <button
                                type="button"
                                class="clipboard-query inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-xs leading-4 font-medium rounded text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150">
                                Copy as PHP array
                            </button>
                        </span>
                    </div>
                    <div v-for="(value, name) in currentRequest.request.query"
                         :key="'query_' + name"
                         class="even:bg-gray-50 odd:bg-gray-50 px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm leading-5 font-medium text-gray-700">
                            @{ name }
                        </dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2 break-all">
                            @{ value }
                        </dd>
                    </div>

                    <div class="px-4 py-5 border-b border-t border-gray-200 sm:px-6 flex justify-between" v-if="Object.keys(currentRequest.request.post).length > 0">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Post Parameters
                        </h3>
                        <span class="inline-flex rounded-md shadow-sm ml-4">
                            <button
                                type="button"
                                class="clipboard-post inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-xs leading-4 font-medium rounded text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150">
                                Copy as PHP array
                            </button>
                        </span>
                    </div>
                    <div v-for="parameter in currentRequest.request.post"
                         :key="'post_' + parameter.name"
                         class="even:bg-gray-50 odd:bg-gray-50 px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm leading-5 font-medium text-gray-700">
                            @{ parameter.name }
                        </dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2 break-all">
                            <span
                                v-if="parameter.is_file">File: @{ parameter.filename } (@{ parameter.mime_type })</span>
                            <span v-else>@{ parameter.value }</span>
                        </dd>
                    </div>

                    <div class="px-4 py-5 border-b border-t border-gray-200 sm:px-6 flex justify-between">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Headers
                        </h3>
                        <span class="inline-flex rounded-md shadow-sm ml-4">
                            <button
                                type="button"
                                class="clipboard-headers inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-xs leading-4 font-medium rounded text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150">
                                Copy as PHP array
                            </button>
                        </span>
                    </div>
                    <div v-for="(value, header) in currentRequest.request.headers"
                         :key="header"
                         class="even:bg-gray-50 odd:bg-gray-50 px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm leading-5 font-medium text-gray-700">
                            @{ header }
                        </dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                            @{ value }
                        </dd>
                    </div>

                    <div class="px-4 py-5 border-b border-t border-gray-200 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Request Body
                        </h3>
                    </div>
                    <div>
                        <pre class="p-6 prettyprint break-all whitespace-pre-wrap">@{ currentRequest.request.body }</pre>
                    </div>
                </div>
            </div>
            <div v-else  class="flex-col bg-white shadow overflow-hidden sm:rounded-lg justify-center items-center flex py-4">
                <h1 class="text-lg">Waiting for incoming requests...</h1>
                <h2 class="text-base mt-4 text-gray-800">Want to try it out? Here's an example request:</h2>
                <pre class="mt-4 bg-gray-800 text-white p-4 rounded shadow text-green-400"><code>curl --header "Content-Type: application/json" \
  --request POST \
  --data '{"username":"Marcel","password":"supersecret","this is a":"test"}' \
{{ route('collect', [$dump->name]) }}</code></pre>
            </div>
        </div>
    </div>
</div>

<script>
    new Vue({
        el: '#app',

        delimiters: ['@{', '}'],

        data: {
            search: '',
            currentRequest: null,
            view: 'request',
            activeTab: 'raw',
            requests: [],
            maxDumps: {{ config('httpdump.max_dumps') }},
        },

        computed: {
            filteredRequests: function() {
                if (this.search === '') {
                    return this.requests;
                }
                return this.requests.filter(request => {
                    return request.request.uri.indexOf(this.search) !== -1;
                });
            },
        },

        methods: {
            clearRequests: function() {
                fetch('/api/requests/clear/{{ $dump->name }}');
                this.requests = []
                this.currentRequest = null;
            },
            toPhpArray: function(rows, variableName) {
                let output = `$${variableName} = [\n`;

                for (let key in rows) {
                    let value = rows[key];

                    if (typeof value.value !== 'undefined') {
                        value = value.value;
                    }

                    output += `    '${key}' => '${value}',\n`;
                }

                output += `];`;

                return output;
            },
            setRequest: function (request) {
                this.currentRequest = request;

                this.$nextTick(function () {
                    new ClipboardJS('.clipboard');

                    new ClipboardJS('.clipboard-query', {
                        text: (trigger) => {
                            return this.toPhpArray(this.currentRequest.request.query, 'queryParameters');
                        }
                    });

                    new ClipboardJS('.clipboard-post', {
                        text: (trigger) => {
                            return this.toPhpArray(this.currentRequest.request.post, 'postParameters');
                        }
                    });

                    new ClipboardJS('.clipboard-headers', {
                        text: (trigger) => {
                            return this.toPhpArray(this.currentRequest.request.headers, 'headers');
                        }
                    });
                });
            },
            setView: function (view) {
                this.view = view;
            },
            replay: function (request) {
                fetch('/api/replay/' + request.id);
            },
            connect: function () {
                Echo.channel('dump-{{ $dump->name }}')
                .listen('IncomingRequest', (e) => {
                    this.loadDumps();
                })
            },
            loadDumps: function () {
                fetch('/api/dumps/{{ $dump->name }}')
                    .then((response) => {
                        return response.json();
                    })
                    .then((data) => {
                        this.requests = data.requests;
                    });
            },
        },

        mounted: function () {
            this.connect();

            this.loadDumps();
        }
    });
</script>
</body>
</html>
