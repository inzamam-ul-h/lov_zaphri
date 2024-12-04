<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $model = new Country();
        $model->code = "AL";
        //$model->name_fr = "ألبانيا";
        $model->name =  "Albania";
        $model->tel = "+355";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "DZ";
        //$model->name_fr = "الجزائر";
        $model->name =  "Algeria";
        $model->tel = "+213";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "AS";
        //$model->name_fr = "ساموا الأمريكية";
        $model->name =  "American Samoa";
        $model->tel = "+1-684";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "AD";
        //$model->name_fr = "أندورا";
        $model->name =  "Andorra";
        $model->tel = "+376";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "AO";
        //$model->name_fr = "أنغولا";
        $model->name =  "Angola";
        $model->tel = "+244";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "AI";
        //$model->name_fr = "أنغيلا";
        $model->name =  "Anguilla";
        $model->tel = "+1-264";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "AR";
        //$model->name_fr = "الأرجنتين";
        $model->name =  "Argentina";
        $model->tel = "+54";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "AM";
        //$model->name_fr = "أرمينيا";
        $model->name =  "Armenia";
        $model->tel = "+374";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "AW";
        //$model->name_fr = "أروبا";
        $model->name =  "Aruba";
        $model->tel = "+297";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "AU";
        //$model->name_fr = "أستراليا";
        $model->name =  "Australia";
        $model->tel = "+61";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "AT";
        //$model->name_fr = "النمسا";
        $model->name =  "Austria";
        $model->tel = "+43";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "AZ";
        //$model->name_fr = "أذربيجان";
        $model->name =  "Azerbaijan";
        $model->tel = "+994";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "BS";
        //$model->name_fr = "جزر البهاما";
        $model->name =  "Bahamas";
        $model->tel = "+1-242";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "BH";
        //$model->name_fr = "البحرين";
        $model->name =  "Bahrain";
        $model->tel = "+973";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "BD";
        //$model->name_fr = "بنغلاديش";
        $model->name =  "Bangladesh";
        $model->tel = "+880";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "BB";
        //$model->name_fr = "بربادوس";
        $model->name =  "Barbados";
        $model->tel = "+1-246";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "BY";
        //$model->name_fr = "روسيا البيضاء";
        $model->name =  "Belarus";
        $model->tel = "+375";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "BE";
        //$model->name_fr = "بلجيكا";
        $model->name =  "Belgium";
        $model->tel = "+32";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "BZ";
        //$model->name_fr = "بليز";
        $model->name =  "Belize";
        $model->tel = "+501";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "BJ";
        //$model->name_fr = "بنين";
        $model->name =  "Benin";
        $model->tel = "+229";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "BM";
        //$model->name_fr = "برمودا";
        $model->name =  "Bermuda";
        $model->tel = "+1-441";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "BT";
        //$model->name_fr = "بوتان";
        $model->name =  "Bhutan";
        $model->tel = "+975";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "BO";
        //$model->name_fr = "بوليفيا";
        $model->name =  "Bolivia";
        $model->tel = "+591";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "BA";
        //$model->name_fr = "البوسنة والهرسك";
        $model->name =  "Bosnia and Herzegovina";
        $model->tel = "+387";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "BW";
        //$model->name_fr = "بوتسوانا";
        $model->name =  "Botswana";
        $model->tel = "+267";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "BR";
        //$model->name_fr = "البرازيل";
        $model->name =  "Brazil";
        $model->tel = "+55";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "VG";
        //$model->name_fr = "جزر فيرجن البريطانية";
        $model->name =  "British Virgin Islands";
        $model->tel = "+1-284";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "IO";
        //$model->name_fr = "إقليم المحيط الهندي البريطاني";
        $model->name =  "British Indian Ocean Territory";
        $model->tel = "+246";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "BN";
        //$model->name_fr = "بروناي دار السلام";
        $model->name =  "Brunei Darussalam";
        $model->tel = "+673";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "BG";
        //$model->name_fr = "بلغاريا";
        $model->name =  "Bulgaria";
        $model->tel = "+359";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "BF";
        //$model->name_fr = "بوركينا فاسو";
        $model->name =  "Burkina Faso";
        $model->tel = "+226";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "BI";
        //$model->name_fr = "بوروندي";
        $model->name =  "Burundi";
        $model->tel = "+257";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "KH";
        //$model->name_fr = "كمبوديا";
        $model->name =  "Cambodia";
        $model->tel = "+855";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "CM";
        //$model->name_fr = "الكاميرون";
        $model->name =  "Cameroon";
        $model->tel = "+237";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "CA";
        //$model->name_fr = "كندا";
        $model->name =  "Canada";
        $model->tel = "+1";
        $model->status = 1;
        $model->save();


        $model = new Country();
        $model->code = "CV";
        //$model->name_fr = "الرأس الأخضر";
        $model->name =  "Cape Verde";
        $model->tel = "+238";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "KY";
        //$model->name_fr = "جزر كايمان";
        $model->name =  "Cayman Islands";
        $model->tel = "+1-345";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "CF";
        //$model->name_fr = "افريقيا الوسطى";
        $model->name =  "Central African Republic";
        $model->tel = "+236";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "TD";
        //$model->name_fr = "تشاد";
        $model->name =  "Chad";
        $model->tel = "+235";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "CL";
        //$model->name_fr = "تشيلي";
        $model->name =  "Chile";
        $model->tel = "+56";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "CN";
        //$model->name_fr = "الصين";
        $model->name =  "China";
        $model->tel = "+86";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "HK";
        //$model->name_fr = "هونغ كونغ";
        $model->name =  "Hong Kong";
        $model->tel = "+852";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "MO";
        //$model->name_fr = "ماكاو";
        $model->name =  "Macao";
        $model->tel = "+853";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "CX";
        //$model->name_fr = "جزيرة الكريسماس";
        $model->name =  "Christmas Island";
        $model->tel = "+61";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "CC";
        //$model->name_fr = "جزر كوكوس (كيلينغ)";
        $model->name =  "Cocos (Keeling) Islands";
        $model->tel = "+61";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "CO";
        //$model->name_fr = "كولومبيا";
        $model->name =  "Colombia";
        $model->tel = "+57";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "KM";
        //$model->name_fr = "جزر القمر";
        $model->name =  "Comoros";
        $model->tel = "+269";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "CK";
        //$model->name_fr = "جزر كوك";
        $model->name =  "Cook Islands";
        $model->tel = "+682";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "CR";
        //$model->name_fr = "كوستا ريكا";
        $model->name =  "Costa Rica";
        $model->tel = "+506";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "HR";
        //$model->name_fr = "كرواتيا";
        $model->name =  "Croatia";
        $model->tel = "+385";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "CU";
        //$model->name_fr = "كوبا";
        $model->name =  "Cuba";
        $model->tel = "+53";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "CY";
        //$model->name_fr = "قبرص";
        $model->name =  "Cyprus";
        $model->tel = "+357";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "CZ";
        //$model->name_fr = "الجمهورية التشيكية";
        $model->name =  "Czech Republic";
        $model->tel = "+420";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "DK";
        //$model->name_fr = "الدنمارك";
        $model->name =  "Denmark";
        $model->tel = "+45";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "DJ";
        //$model->name_fr = "جيبوتي";
        $model->name =  "Djibouti";
        $model->tel = "+253";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "DM";
        //$model->name_fr = "دومينيكا";
        $model->name =  "Dominica";
        $model->tel = "+1-767";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "DO";
        //$model->name_fr = "جمهورية الدومينيكان";
        $model->name =  "Dominican Republic";
        $model->tel = "+1-809";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "EC";
        //$model->name_fr = "الاكوادور";
        $model->name =  "Ecuador";
        $model->tel = "+593";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "EG";
        //$model->name_fr = "مصر";
        $model->name =  "Egypt";
        $model->tel = "+20";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "SV";
        //$model->name_fr = "السلفادور";
        $model->name =  "El Salvador";
        $model->tel = "+503";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "GQ";
        //$model->name_fr = "غينيا الاستوائية";
        $model->name =  "Equatorial Guinea";
        $model->tel = "+240";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "ER";
        //$model->name_fr = "إريتريا";
        $model->name =  "Eritrea";
        $model->tel = "+291";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "EE";
        //$model->name_fr = "استونيا";
        $model->name =  "Estonia";
        $model->tel = "+372";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "ET";
        //$model->name_fr = "أثيوبيا";
        $model->name =  "Ethiopia";
        $model->tel = "+251";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "FO";
        //$model->name_fr = "جزر فارو";
        $model->name =  "Faroe Islands";
        $model->tel = "+298";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "FJ";
        //$model->name_fr = "فيجي";
        $model->name =  "Fiji";
        $model->tel = "+679";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "FI";
        //$model->name_fr = "فنلندا";
        $model->name =  "Finland";
        $model->tel = "+358";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "FR";
        //$model->name_fr = "فرنسا";
        $model->name =  "France";
        $model->tel = "+33";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "GF";
        //$model->name_fr = "جيانا الفرنسية";
        $model->name =  "French Guiana";
        $model->tel = "+689";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "GA";
        //$model->name_fr = "الغابون";
        $model->name =  "Gabon";
        $model->tel = "+241";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "GM";
        //$model->name_fr = "غامبيا";
        $model->name =  "Gambia";
        $model->tel = "+220";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "GE";
        //$model->name_fr = "جورجيا";
        $model->name =  "Georgia";
        $model->tel = "+995";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "DE";
        //$model->name_fr = "ألمانيا";
        $model->name =  "Germany";
        $model->tel = "+49";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "GH";
        //$model->name_fr = "غانا";
        $model->name =  "Ghana";
        $model->tel = "+233";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "GI";
        //$model->name_fr = "جبل طارق";
        $model->name =  "Gibraltar";
        $model->tel = "+350";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "GR";
        //$model->name_fr = "يونان";
        $model->name =  "Greece";
        $model->tel = "+30";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "GL";
        //$model->name_fr = "غرينلاند";
        $model->name =  "Greenland";
        $model->tel = "+299";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "GD";
        //$model->name_fr = "غرينادا";
        $model->name =  "Grenada";
        $model->tel = "+1-473";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "GU";
        //$model->name_fr = "غوام";
        $model->name =  "Guam";
        $model->tel = "+1-671";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "GT";
        //$model->name_fr = "غواتيمالا";
        $model->name =  "Guatemala";
        $model->tel = "+502";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "GN";
        //$model->name_fr = "غينيا";
        $model->name =  "Guinea";
        $model->tel = "+224";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "GW";
        //$model->name_fr = "غينيا-بيساو";
        $model->name =  "Guinea-Bissau";
        $model->tel = "+245";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "GY";
        //$model->name_fr = "غيانا";
        $model->name =  "Guyana";
        $model->tel = "+592";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "HT";
        //$model->name_fr = "هايتي";
        $model->name =  "Haiti";
        $model->tel = "+509";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "HN";
        //$model->name_fr = "هندوراس";
        $model->name =  "Honduras";
        $model->tel = "+504";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "HU";
        //$model->name_fr = "هنغاريا";
        $model->name =  "Hungary";
        $model->tel = "+36";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "IS";
        //$model->name_fr = "أيسلندا";
        $model->name =  "Iceland";
        $model->tel = "+354";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "IN";
        //$model->name_fr = "الهند";
        $model->name =  "India";
        $model->tel = "+91";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "ID";
        //$model->name_fr = "أندونيسيا";
        $model->name =  "Indonesia";
        $model->tel = "+62";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "IR";
        //$model->name_fr = "جمهورية إيران الإسلامية";
        $model->name =  "Iran, Islamic Republic of";
        $model->tel = "+98";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "IQ";
        //$model->name_fr = "العراق";
        $model->name =  "Iraq";
        $model->tel = "+964";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "IE";
        //$model->name_fr = "أيرلندا";
        $model->name =  "Ireland";
        $model->tel = "+353";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "IM";
        //$model->name_fr = "جزيرة مان";
        $model->name =  "Isle of Man";
        $model->tel = "+44-1624";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "IL";
        //$model->name_fr = "إسرائيل";
        $model->name =  "Israel";
        $model->tel = "+972";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "IT";
        //$model->name_fr = "إيطاليا";
        $model->name =  "Italy";
        $model->tel = "+39";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "JM";
        //$model->name_fr = "جامايكا";
        $model->name =  "Jamaica";
        $model->tel = "+1-876";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "JP";
        //$model->name_fr = "اليابان";
        $model->name =  "Japan";
        $model->tel = "+81";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "JE";
        //$model->name_fr = "جيرسي";
        $model->name =  "Jersey";
        $model->tel = "+44-1534";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "JO";
        //$model->name_fr = "الأردن";
        $model->name =  "Jordan";
        $model->tel = "+962";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "KZ";
        //$model->name_fr = "كازاخستان";
        $model->name =  "Kazakhstan";
        $model->tel = "+7";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "KE";
        //$model->name_fr = "كينيا";
        $model->name =  "Kenya";
        $model->tel = "+254";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "KI";
        //$model->name_fr = "كيريباس";
        $model->name =  "Kiribati";
        $model->tel = "+686";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "KW";
        //$model->name_fr = "الكويت";
        $model->name =  "Kuwait";
        $model->tel = "+965";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "KG";
        //$model->name_fr = "قيرغيزستان";
        $model->name =  "Kyrgyzstan";
        $model->tel = "+996";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "LV";
        //$model->name_fr = "لاتفيا";
        $model->name =  "Latvia";
        $model->tel = "+371";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "LB";
        //$model->name_fr = "لبنان";
        $model->name =  "Lebanon";
        $model->tel = "+961";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "LS";
        //$model->name_fr = "ليسوتو";
        $model->name =  "Lesotho";
        $model->tel = "+266";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "LR";
        //$model->name_fr = "ليبيريا";
        $model->name =  "Liberia";
        $model->tel = "+231";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "LY";
        //$model->name_fr = "ليبيا";
        $model->name =  "Libya";
        $model->tel = "+218";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "LI";
        //$model->name_fr = "ليشتنشتاين";
        $model->name =  "Liechtenstein";
        $model->tel = "+423";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "LT";
        //$model->name_fr = "ليتوانيا";
        $model->name =  "Lithuania";
        $model->tel = "+370";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "LU";
        //$model->name_fr = "لوكسمبورغ";
        $model->name =  "Luxembourg";
        $model->tel = "+352";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "MK";
        //$model->name_fr = "مقدونيا، جمهورية";
        $model->name =  "Macedonia";
        $model->tel = "+389";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "MG";
        //$model->name_fr = "مدغشقر";
        $model->name =  "Madagascar";
        $model->tel = "+261";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "MW";
        //$model->name_fr = "ملاوي";
        $model->name =  "Malawi";
        $model->tel = "+265";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "MY";
        //$model->name_fr = "ماليزيا";
        $model->name =  "Malaysia";
        $model->tel = "+60";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "MV";
        //$model->name_fr = "جزر المالديف";
        $model->name =  "Maldives";
        $model->tel = "+960";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "ML";
        //$model->name_fr = "مالي";
        $model->name =  "Mali";
        $model->tel = "+223";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "MT";
        //$model->name_fr = "مالطا";
        $model->name =  "Malta";
        $model->tel = "+356";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "MH";
        //$model->name_fr = "جزر مارشال";
        $model->name =  "Marshall Islands";
        $model->tel = "+692";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "MR";
        //$model->name_fr = "موريتانيا";
        $model->name =  "Mauritania";
        $model->tel = "+222";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "MU";
        //$model->name_fr = "موريشيوس";
        $model->name =  "Mauritius";
        $model->tel = "+230";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "YT";
        //$model->name_fr = "مايوت";
        $model->name =  "Mayotte";
        $model->tel = "+262";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "MX";
        //$model->name_fr = "المكسيك";
        $model->name =  "Mexico";
        $model->tel = "+52";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "FM";
        //$model->name_fr = "ولايات ميكرونيزيا الموحدة";
        $model->name =  "Micronesia";
        $model->tel = "+691";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "MD";
        //$model->name_fr = "مولدوفا";
        $model->name =  "Moldova";
        $model->tel = "+373";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "MC";
        //$model->name_fr = "موناكو";
        $model->name =  "Monaco";
        $model->tel = "+377";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "MN";
        //$model->name_fr = "منغوليا";
        $model->name =  "Mongolia";
        $model->tel = "+976";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "ME";
        //$model->name_fr = "الجبل الأسود";
        $model->name =  "Montenegro";
        $model->tel = "+382";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "MS";
        //$model->name_fr = "مونتسيرات";
        $model->name =  "Montserrat";
        $model->tel = "+1-664";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "MA";
        //$model->name_fr = "المغرب";
        $model->name =  "Morocco";
        $model->tel = "+212";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "MZ";
        //$model->name_fr = "موزمبيق";
        $model->name =  "Mozambique";
        $model->tel = "+258";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "MM";
        //$model->name_fr = "ميانمار";
        $model->name =  "Myanmar";
        $model->tel = "+95";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "NA";
        //$model->name_fr = "ناميبيا";
        $model->name =  "Namibia";
        $model->tel = "+264";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "NR";
        //$model->name_fr = "ناورو";
        $model->name =  "Nauru";
        $model->tel = "+674";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "NP";
        //$model->name_fr = "نيبال";
        $model->name =  "Nepal";
        $model->tel = "+977";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "NL";
        //$model->name_fr = "هولندا";
        $model->name =  "Netherlands";
        $model->tel = "+31";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "AN";
        //$model->name_fr = "جزر الأنتيل الهولندية";
        $model->name =  "Netherlands Antilles";
        $model->tel = "+599";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "NC";
        //$model->name_fr = "كاليدونيا الجديدة";
        $model->name =  "New Caledonia";
        $model->tel = "+687";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "NZ";
        //$model->name_fr = "نيوزيلندا";
        $model->name =  "New Zealand";
        $model->tel = "+64";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "NI";
        //$model->name_fr = "نيكاراغوا";
        $model->name =  "Nicaragua";
        $model->tel = "+505";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "NE";
        //$model->name_fr = "النيجر";
        $model->name =  "Niger";
        $model->tel = "+227";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "NG";
        //$model->name_fr = "نيجيريا";
        $model->name =  "Nigeria";
        $model->tel = "+234";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "NU";
        //$model->name_fr = "نيوي";
        $model->name =  "Niue";
        $model->tel = "+683";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "NO";
        //$model->name_fr = "النرويج";
        $model->name =  "Norway";
        $model->tel = "+47";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "OM";
        //$model->name_fr = "عمان";
        $model->name =  "Oman";
        $model->tel = "+968";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "PK";
        //$model->name_fr = "باكستان";
        $model->name =  "Pakistan";
        $model->tel = "+92";
        $model->status = 1;
        $model->save();


        $model = new Country();
        $model->code = "PW";
        //$model->name_fr = "بالاو";
        $model->name =  "Palau";
        $model->tel = "+680";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "PS";
        //$model->name_fr = "فلسطين";
        $model->name =  "Palestinian";
        $model->tel = "+972";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "PA";
        //$model->name_fr = "بناما";
        $model->name =  "Panama";
        $model->tel = "+507";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "PY";
        //$model->name_fr = "باراغواي";
        $model->name =  "Paraguay";
        $model->tel = "+595";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "PE";
        //$model->name_fr = "بيرو";
        $model->name =  "Peru";
        $model->tel = "+51";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "PH";
        //$model->name_fr = "الفلبين";
        $model->name =  "Philippines";
        $model->tel = "+63";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "PN";
        //$model->name_fr = "بيتكيرن";
        $model->name =  "Pitcairn";
        $model->tel = "+870";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "PL";
        //$model->name_fr = "بولندا";
        $model->name =  "Poland";
        $model->tel = "+48";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "PT";
        //$model->name_fr = "البرتغال";
        $model->name =  "Portugal";
        $model->tel = "+351";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "PR";
        //$model->name_fr = "بويرتو ريكو";
        $model->name =  "Puerto Rico";
        $model->tel = "+1-787";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "QA";
        //$model->name_fr = "قطر";
        $model->name =  "Qatar";
        $model->tel = "+974";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "RO";
        //$model->name_fr = "رومانيا";
        $model->name =  "Romania";
        $model->tel = "+40";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "RU";
        //$model->name_fr = "الفيدرالية الروسية";
        $model->name =  "Russian Federation";
        $model->tel = "+7";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "RW";
        //$model->name_fr = "رواندا";
        $model->name =  "Rwanda";
        $model->tel = "+250";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "SH";
        //$model->name_fr = "سانت هيلينا";
        $model->name =  "Saint Helena";
        $model->tel = "+290";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "KN";
        //$model->name_fr = "سانت كيتس ونيفيس";
        $model->name =  "Saint Kitts and Nevis";
        $model->tel = "+1-869";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "LC";
        //$model->name_fr = "سانت لوسيا";
        $model->name =  "Saint Lucia";
        $model->tel = "+1-758";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "PM";
        //$model->name_fr = "سان بيار وميكلون";
        $model->name =  "Saint Pierre and Miquelon";
        $model->tel = "+508";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "VC";
        //$model->name_fr = "سانت فنسنت وجزر غرينادين";
        $model->name =  "Saint Vincent and Grenadines";
        $model->tel = "+1-784";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "WS";
        //$model->name_fr = "ساموا";
        $model->name =  "Samoa";
        $model->tel = "+685";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "SM";
        //$model->name_fr = "سان مارينو";
        $model->name =  "San Marino";
        $model->tel = "+378";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "ST";
        //$model->name_fr = "ساو تومي وبرينسيبي";
        $model->name =  "Sao Tome and Principe";
        $model->tel = "+239";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "SA";
        //$model->name_fr = "المملكة العربية السعودية";
        $model->name =  "Saudi Arabia";
        $model->tel = "+966";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "SN";
        //$model->name_fr = "السنغال";
        $model->name =  "Senegal";
        $model->tel = "+221";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "RS";
        //$model->name_fr = "صربيا";
        $model->name =  "Serbia";
        $model->tel = "+381";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "SC";
        //$model->name_fr = "سيشيل";
        $model->name =  "Seychelles";
        $model->tel = "+248";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "SL";
        //$model->name_fr = "سيرا ليون";
        $model->name =  "Sierra Leone";
        $model->tel = "+232";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "SG";
        //$model->name_fr = "سنغافورة";
        $model->name =  "Singapore";
        $model->tel = "+65";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "SK";
        //$model->name_fr = "سلوفاكيا";
        $model->name =  "Slovakia";
        $model->tel = "+421";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "SI";
        //$model->name_fr = "سلوفينيا";
        $model->name =  "Slovenia";
        $model->tel = "+386";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "SB";
        //$model->name_fr = "جزر سليمان";
        $model->name =  "Solomon Islands";
        $model->tel = "+677";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "SO";
        //$model->name_fr = "الصومال";
        $model->name =  "Somalia";
        $model->tel = "+252";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "ZA";
        //$model->name_fr = "جنوب أفريقيا";
        $model->name =  "South Africa";
        $model->tel = "+27";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "ES";
        //$model->name_fr = "إسبانيا";
        $model->name =  "Spain";
        $model->tel = "+34";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "LK";
        //$model->name_fr = "سيريلانكا";
        $model->name =  "Sri Lanka";
        $model->tel = "+94";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "SD";
        //$model->name_fr = "السودان";
        $model->name =  "Sudan";
        $model->tel = "+249";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "SR";
        //$model->name_fr = "سورينام";
        $model->name =  "Suriname";
        $model->tel = "+597";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "SJ";
        //$model->name_fr = "جزر سفالبارد وجان ماين";
        $model->name =  "Svalbard and Jan Mayen Islands";
        $model->tel = "+47";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "SZ";
        //$model->name_fr = "سوازيلاند";
        $model->name =  "Swaziland";
        $model->tel = "+268";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "SE";
        //$model->name_fr = "السويد";
        $model->name =  "Sweden";
        $model->tel = "+46";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "CH";
        //$model->name_fr = "سويسرا";
        $model->name =  "Switzerland";
        $model->tel = "+41";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "SY";
        //$model->name_fr = "سوريا";
        $model->name =  "Syrian Arab Republic";
        $model->tel = "+963";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "TW";
        //$model->name_fr = "تايوان، جمهورية الصين";
        $model->name =  "Taiwan, Republic of China";
        $model->tel = "+886";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "TJ";
        //$model->name_fr = "طاجيكستان";
        $model->name =  "Tajikistan";
        $model->tel = "+992";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "TZ";
        //$model->name_fr = "تنزانيا";
        $model->name =  "Tanzania";
        $model->tel = "+255";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "TH";
        //$model->name_fr = "تايلاند";
        $model->name =  "Thailand";
        $model->tel = "+66";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "TG";
        //$model->name_fr = "توغو";
        $model->name =  "Togo";
        $model->tel = "+228";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "TK";
        //$model->name_fr = "توكيلاو";
        $model->name =  "Tokelau";
        $model->tel = "+690";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "TO";
        //$model->name_fr = "تونغا";
        $model->name =  "Tonga";
        $model->tel = "+676";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "TT";
        //$model->name_fr = "ترينداد وتوباغو";
        $model->name =  "Trinidad and Tobago";
        $model->tel = "+1-868";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "TN";
        //$model->name_fr = "تونس";
        $model->name =  "Tunisia";
        $model->tel = "+216";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "TR";
        //$model->name_fr = "تركيا";
        $model->name =  "Turkey";
        $model->tel = "+90";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "TM";
        //$model->name_fr = "تركمانستان";
        $model->name =  "Turkmenistan";
        $model->tel = "+993";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "TC";
        //$model->name_fr = "جزر تركس وكايكوس";
        $model->name =  "Turks and Caicos Islands";
        $model->tel = "+1-649";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "TV";
        //$model->name_fr = "توفالو";
        $model->name =  "Tuvalu";
        $model->tel = "+688";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "UG";
        //$model->name_fr = "أوغندا";
        $model->name =  "Uganda";
        $model->tel = "+256";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "UA";
        //$model->name_fr = "أوكرانيا";
        $model->name =  "Ukraine";
        $model->tel = "+380";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "AE";
        //$model->name_fr = "الإمارات العربية المتحدة";
        $model->name =  "United Arab Emirates";
        $model->tel = "+971";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "GB";
        //$model->name_fr = "المملكة المتحدة";
        $model->name =  "United Kingdom";
        $model->tel = "+44";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "US";
        //$model->name_fr = "الولايات المتحدة الأمريكية";
        $model->name =  "United States of America";
        $model->tel = "+1";
        $model->status = 1;
        $model->save();


        $model = new Country();
        $model->code = "UY";
        //$model->name_fr = "أوروغواي";
        $model->name =  "Uruguay";
        $model->tel = "+598";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "UZ";
        //$model->name_fr = "أوزبكستان";
        $model->name =  "Uzbekistan";
        $model->tel = "+998";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "VU";
        //$model->name_fr = "فانواتو";
        $model->name =  "Vanuatu";
        $model->tel = "+678";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "VE";
        //$model->name_fr = "فنزويلا";
        $model->name =  "Venezuela";
        $model->tel = "+58";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "VN";
        //$model->name_fr = "فيتنام";
        $model->name =  "Viet Nam";
        $model->tel = "+84";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "WF";
        //$model->name_fr = "واليس وفوتونا";
        $model->name =  "Wallis and Futuna Islands";
        $model->tel = "+681";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "YE";
        //$model->name_fr = "اليمن";
        $model->name =  "Yemen";
        $model->tel = "+967";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "ZM";
        //$model->name_fr = "زامبيا";
        $model->name =  "Zambia";
        $model->tel = "+260";
        $model->status = 0;
        $model->save();


        $model = new Country();
        $model->code = "ZW";
        //$model->name_fr = "زيمبابوي";
        $model->name =  "Zimbabwe";
        $model->tel = "+263";
        $model->status = 0;
        $model->save();
    }
}
